<?php
include_once "Product.php";
include_once "Book.php";
include_once "DVD.php";
include_once "Furniture.php";
include_once "Serializer.php";

class DB
{
    private static $instance = null;
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    private function __construct()
    {
        $envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';
        if (file_exists($envFile)) {
            $envVars = parse_ini_file($envFile);
            foreach ($envVars as $key => $value) {
                putenv("$key=$value");
            }
        }
        $this->host = getenv('DB_HOST');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->dbname = getenv('DB_DATABASE');
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    private function connect()
    {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);

        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function getProducts()
    {
        $stmt = $this->conn->prepare("SELECT p.id, p.sku, p.name, p.price, p.type, 
              b.weight, d.size, f.height, f.width, f.length
              FROM Product p
              LEFT JOIN Book b ON p.id = b.id
              LEFT JOIN DVD d ON p.id = d.id
              LEFT JOIN Furniture f ON p.id = f.id");
        $stmt->execute();
        $result = $stmt->get_result();

        $products = array();
        while ($product = $result->fetch_assoc()) {
            array_push($products, Serializer::deserialize($product));
        }

        $products = array_map(function ($product) {
            return Serializer::serialize($product);
        }, $products);

        return json_encode($products);
    }

    public function getProductBySKU($sku)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Product WHERE sku = ?");
        $stmt->bind_param("s", $sku);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    public function deleteProductBySKU($sku)
    {
        $this->conn->autocommit(false);
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare("DELETE p, d, f, b FROM Product p
        LEFT JOIN DVD d ON p.id = d.id
        LEFT JOIN Furniture f ON p.id = f.id
        LEFT JOIN Book b ON p.id = b.id
        WHERE p.sku = ?");
            $stmt->bind_param("s", $sku);
            $stmt->execute();

            if ($stmt->affected_rows == 0) {
                throw new Exception("Can't delete product with sku={$sku}");
            }

            $this->conn->commit();
            $this->conn->autocommit(true);
            return "Product with sku={$sku} deleted";
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(true);
            throw new Exception('Transaction error: ' . $e->getMessage());
        }
    }

    public function deleteProductsBySKUArray($skus)
    {
        $result = [];
        foreach ($skus as $sku) {
            $result[] = $this->deleteProductBySKU($sku);
        }

        return json_encode($result);
    }

    public function addProducts($product)
    {
        try {
            $result = [];

            if (count(array_filter($product)) !== count($product)) {
                $result['error'] = 'fields_required';
            } else {
                $productManager = DB::getInstance();
                $productObject = Serializer::deserialize($product);

                if (is_a($productObject, 'Product')) {
                    if (!is_null($productManager->getProductBySKU($productObject->getSku()))) {
                        $result['error'] = 'already_exists';
                    }

                    if (!isset($result['error'])) {
                        $this->conn->begin_transaction();

                        $productColumns = array_map(function ($value) use ($product) {
                            if (in_array($value['Field'], array_keys($product))) {
                                return $value['Field'];
                            }
                        }, array_values($this->getColumnsInfoByTableName('Product')));

                        $productColumns = array_filter($productColumns);

                        $productValues = array_intersect_key($product, array_flip($productColumns));
                        $productValueStr = "'" . implode("', '", array_values($productValues)) . "'";
                        $productColumnStr = implode(", ", $productColumns);

                        $stmt = $this->conn->prepare("INSERT INTO Product ($productColumnStr) VALUES ($productValueStr)");
                        if ($stmt->execute()) {
                            $productId = $this->conn->insert_id;

                            $typeColumns = array_map(function ($value) use ($product) {
                                if (in_array($value['Field'], array_keys($product))) {
                                    return $value['Field'];
                                }
                            }, array_values($this->getColumnsInfoByTableName($product["type"])));

                            $typeColumns = array_filter($typeColumns);
                            array_unshift($typeColumns, "id");

                            $typeValues = array_intersect_key($product, array_flip($typeColumns));
                            array_unshift($typeValues, $productId);
                            $typeValueStr = "'" . implode("', '", array_values($typeValues)) . "'";
                            $typeColumnStr = implode(", ", $typeColumns);

                            $stmt = $this->conn->prepare("INSERT INTO " . $product["type"] . " ($typeColumnStr) VALUES ($typeValueStr)");
                            $stmt->execute();

                            $this->conn->commit();
                        }
                    }
                } else {
                    $result = $productObject;
                }
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(true);
            $result['error'] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function getColumnsInfoByTableName($tableName)
    {
        $stmt = $this->conn->prepare("SHOW COLUMNS FROM $tableName");

        $stmt->execute();
        $result = $stmt->get_result();

        $columns = array();
        while ($column = $result->fetch_assoc()) {
            array_push($columns, $column);
        }

        return $columns;
    }

    public function getColumnsNameByTableName($tableName)
    {
        $stmt = $this->conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName' ORDER BY ORDINAL_POSITION");
        $stmt->execute();
        $tables = $stmt->get_result();

        while ($row = $tables->fetch_assoc()) {
            if ($row['COLUMN_NAME'] != 'id') {
                $result[] = $row['COLUMN_NAME'];
            }
        }

        return $result;
    }

    public function getAllPropertiesGroupedByType()
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'Product' OR TABLE_NAME = 'Product'");
        $stmt->execute();
        $tables = $stmt->get_result();

        $tableNames = array();
        while ($row = $tables->fetch_assoc()) {
            $tableNames[] = $row['TABLE_NAME'];
        }

        $result = [];
        foreach ($tableNames as $table) {
            $result[0][$table] = $this->getColumnsNameByTableName($table);
        }

        return json_encode($result);
    }
}

