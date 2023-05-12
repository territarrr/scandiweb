<?php
include_once "classes/DB.php";
echo DB::getInstance()->addProducts(json_decode(file_get_contents('php://input'), true));
