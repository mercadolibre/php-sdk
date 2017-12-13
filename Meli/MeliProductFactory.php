<?php

namespace Meli\MeliProductFactory;

use Meli\MeliProduct;

/**
 * MeliProductFactory - handle product's operations, almost always return an instance of MeliProduct as result
 * Accepts a Meli's object for handling requests
 */
class MeliProductFactory
{
    /**
    * Gets a product from MercadoLivre based on product's id
    * @param $meli is an instance of Meli's class for handling requests
    * @param $sku is the product's sku
    * @return an instance of MeliProduct or throws an error
    */
    public static function getProduct(Meli $meli, $sku)
    {
        // Some cool code will born here
    }

    /**
    * Gets a product from MercadoLivre based on product's id
    * @param $meli is an instance of Meli's class for handling requests
    * @param $page is the current page
    * @return an array with the result, empty in case of any product's found or throws an instance of MeliException
    */
    public static function getProductList(Meli $meli, $page = 0)
    {
        // Some cool code will born here
    }
}