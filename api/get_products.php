<?php
include_once "classes/DB.php";
include_once "classes/Serializer.php";
$productManager = DB::getInstance();
$productsArray = $productManager->getProducts();

$productsArray = array_map(function($product) {
    return Serializer::serialize($product);
}, $productsArray);

echo json_encode($productsArray);