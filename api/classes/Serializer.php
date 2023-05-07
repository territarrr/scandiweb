<?php
include_once "classes/DB.php";
include_once "classes/Book.php";
include_once "classes/DVD.php";
include_once "classes/Furniture.php";
include_once "classes/Product.php";

class Serializer
{
    private static $productTypes = [
        'Book' => Book::class,
        'DVD' => DVD::class,
        'Furniture' => Furniture::class,
    ];

    public static function deserialize($data)
    {
        $productManager = DB::getInstance();
        $tableFields = array_merge($productManager->getColumns("Product"), $productManager->getColumns($data["type"]));

        $validEnumValues = ['DVD', 'Book', 'Furniture'];
        $result['error'] = [];
        foreach ($tableFields as $field) {
            if (!array_key_exists($field["Field"], $data)) {
                continue;
            }
            $value = $data[$field["Field"]];
            if ($field["Type"] == 'int' && !is_numeric($value)) {
                $result['error'] = 'invalid_data';
                break;
            } elseif ($field["Type"] == 'varchar(255)' && strlen($value) > 255) {
                $result['error'] = 'invalid_data';
                break;
            } elseif ($field["Type"] == 'decimal(10,2)' && !is_numeric($value)) {
                $result['error'] = 'invalid_data';
                break;
            } elseif ($field["Type"] == "enum('DVD','Book','Furniture')" && !in_array($value, $validEnumValues)) {
                $result['error'] = 'invalid_data';
                break;
            }
        }
        if(count($result['error']) == 0) {

            $productType = $data['type'] ?? null;
            if (!isset(self::$productTypes[$productType])) {
                return null;
            }
            $productClass = self::$productTypes[$productType];

            $reflection = new ReflectionClass($productClass);
            $properties = $reflection->getDefaultProperties();
            $product = $reflection->newInstanceWithoutConstructor();

            foreach ($properties as $key => $value) {
                $reflectionProperty = $reflection->getProperty($key);
                $reflectionProperty->setAccessible(false);
                $reflectionProperty->setValue($product, $data[$key]);
            }

            return $product;
        } else {
            return $result;
        }
    }

    public static function serialize($data): ?array
    {
        $className = get_class($data);
        $reflection = new ReflectionClass($className);
        $properties = $reflection->getDefaultProperties();
        $result = array();

        foreach ($properties as $key => $value) {
            $result[$key] = $reflection->getProperty($key)->getValue($data);
        }

        return $result;
    }
}