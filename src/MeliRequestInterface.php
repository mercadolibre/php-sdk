<?php 

interface MeliRequestInterface
{
	/**
	* Receives data for making a request
	* 
	* @param string $method
	* @param string $uri beind appended with root
	* @param array $data receiving usually accepts ['json' => []] for JSON requests and ['query' => []] for querying the data in GET method
	* @param bool $append_access_token
	* @return array with at least 'status' index with HTTP Status Code and 'body' index containing the response(if is JSON, must be decoded as array)
	*/
    public function request($method, array $data = [], $append_access_token);
}