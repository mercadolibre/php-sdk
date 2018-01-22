<?php

namespace Meli;

// use \Meli\Meli as Meli;

/**
 * Product
 */
class Product
{
    private $meli;

    /**
     * Receives a Meli instance as reference for making requests
     * @author Matheus Hernandes {github.com/onhernandes}
     * @return void
     */
    public function __construct(Meli $meli)
    {
        $this->meli = &$meli;
    }

    /**
    * Gets a product based on product's sku
    * @param $sku is the product's sku
    * @return an instance of MeliProduct or throws an error
    */
    public function getProduct($sku)
    {
        // Some cool code will born here
    }

    /**
    * Gets a list of products
    * @param $page is the current page
    * @return an array with the result, empty in case of any product's found or throws an instance of MeliException
    */
    public function getProductList($page = 0)
    {
        // Some cool code will born here
    }

    /**
    * Remove $meli from debug functions
    * 
    * @return void
    */
    public function __debugInfo()
    {
        $result = get_object_vars($this);
        unset($result['meli']);
        return $result;
    }
}