<?php

namespace Meli;

// use \Meli\Meli as Meli;

/**
 * MeliProduct - this class will handle product's operations and also will be an instance of a product itself
 */
class MeliUser extends Meli
{
	public $sku;

	public function __construct($client_id, $client_secret, $access_token = null, $refresh_token = null)
	{
		parent::__construct($client_id, $client_secret, $access_token, $refresh_token);
	}

    /**
    * Gets the user itself
    * @param $sku is the product's sku
    * @return an instance of MeliProduct or throws an error
    */
    public function getMe()
    {
    	return parent::request('GET', 'users/me');
        // Some cool code will born here
    }

    /**
     * @param $id the user's id
     * @return object instance as user
     */
    public function getUser($id)
    {
        return parent::request('GET', 'user');
    }
}