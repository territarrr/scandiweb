<?php

abstract class Product
{
    protected $sku;
    protected $name;
    protected $price;
    protected $type;

    public function __construct($sku, $name, $price)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }

    public function getSku()
    {
        return $this->sku;
    }
}

