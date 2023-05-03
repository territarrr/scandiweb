<?php

class Book extends Product
{
    protected $weight;

    public function __construct($sku, $name, $price, $weight)
    {
        parent::__construct($sku, $name, $price);
        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}
