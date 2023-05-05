<?php
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

    public static function deserialize($data): ?Product
    {
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