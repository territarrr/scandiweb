<?php
include_once "classes/DB.php";
include_once "classes/Serializer.php";

$_POST = json_decode(file_get_contents('php://input'), true);
$result = [];

if (count(array_filter($_POST)) !== count($_POST)) {
    $result['error'] = 'fields_required';
}

$productManager = DB::getInstance();
$tableFields = array_merge($productManager->getColumns("Product"), $productManager->getColumns($_POST["type"]));

$validEnumValues = ['DVD', 'Book', 'Furniture'];

foreach ($tableFields as $field) {
    if (!array_key_exists($field["Field"], $_POST)) {
        continue;
    }
    $value = $_POST[$field["Field"]];
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

$product = Serializer::deserialize($_POST);

if (!is_null($productManager->getProductBySKU($product->getSku()))) {
    $result['error'] = 'already_exists';
}


if (!isset($result['error'])) {
    if(($transactionError = $productManager->addProducts(Serializer::serialize($product))) !== true) {
        $result['error'] = $transactionError;
    }
}

echo json_encode($result);