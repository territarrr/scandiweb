<?php
include_once "classes/DB.php";
$productManager = DB::getInstance();
$_POST = json_decode(file_get_contents('php://input'), true);
foreach ($_POST['skus'] as $sku) {
    $result[] = $productManager->deleteProductBySKU($sku);
}

echo json_encode($result);