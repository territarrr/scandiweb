<?php
require_once "./classes/DB.php";
$classes = get_declared_classes();
$result = [];

foreach ($classes as $subclass) {
    if (is_subclass_of($subclass, "Product") || $subclass == "Product") {
        $reflection = new ReflectionClass($subclass);
        $properties = $reflection->getDefaultProperties();
        $result[0][$subclass] = array_keys($properties);
    }
}
echo json_encode($result);