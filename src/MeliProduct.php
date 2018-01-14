<?php

namespace Meli;

// use \Meli\Meli as Meli;

/**
 * MeliProduct - this class will handle product's operations and also will be an instance of a product itself
 */
class MeliProduct extends Meli
{
	public $sku;

	public function __construct($client_id, $client_secret, $access_token = null, $refresh_token = null)
	{
		parent::__construct($client_id, $client_secret, $access_token, $refresh_token);
	}

    /**
    * Gets a product from MercadoLivre based on product's id
    * @param $sku is the product's sku
    * @return an instance of MeliProduct or throws an error
    */
    public function getProduct($sku)
    {
    	return parent::request('GET', 'users/me');
        // Some cool code will born here
    }

    /**
    * Gets a product from MercadoLivre based on product's id
    * @param $page is the current page
    * @return an array with the result, empty in case of any product's found or throws an instance of MeliException
    */
    public function getProductList($page = 0)
    {
        // Some cool code will born here
    }
}