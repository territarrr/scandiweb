<?php
include_once "classes/DB.php";
include_once "classes/Serializer.php";

$_POST = json_decode(file_get_contents('php://input'), true);
$result = [];

if (count(array_filter($_POST)) !== count($_POST)) {
    $result['error'] = 'fields_required';
} else {
    $productManager = DB::getInstance();
    $product = Serializer::deserialize($_POST);
        if (is_a($product, 'Product')) {
            if (!is_null($productManager->getProductBySKU($product->getSku()))) {
                $result['error'] = 'already_exists';
            }

            if (!isset($result['error'])) {
                if (($transactionError = $productManager->addProducts(Serializer::serialize($product))) !== true) {
                    $result['error'] = $transactionError;
                }
            }
        } else {
            $result['error'] = $product['error'];
        }
}
echo json_encode($result);