<?php 

/**
 * FakerRequest creates fake requests 
 */
class FakerRequest implements \Meli\MeliRequestInterface
{
    /**
    * Initiates the object
    * 
    * @param string $country the current country
    * @param array $credentials the credentials
    */
    public function __construct($country = '', $credentials = [])
    {
        
    }

    /**
    * Fake request
    * 
    * @param string $method for HTTP Method 
    * @param string $uri for URI 
    * @param array $data to be sent within request 
    * @param bool $append_access_token if must append the access token within the request
    * 
    * @return array with body, status and headers
    */
    public function request($method, $uri, $data = [], $append_access_token = false)
    {
        
    }
}