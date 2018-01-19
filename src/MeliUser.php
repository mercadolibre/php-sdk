<?php

namespace Meli;

/**
 * MeliProduct - this class will handle product's operations and also will be an instance of a product itself
 */
class MeliUser
{
    private $meli;

    /**
     * Receives a Meli instance as reference for making requests
     * @author Matheus Hernandes {github.com/onhernandes}
     * @return void
     */
	public function __construct(Meli &$meli, array $data = [])
	{
        $this->meli = $meli;
        $this->fill($data);
	}

    /**
    * Gets the user itself
    * @return an instance of the user
    */
    public function getMe()
    {
    	$response = $this->meli->request('GET', '/users/me');

        if ($response['status'] == 200 && is_array($response['body'])) {
            $this->fill($response['body']);
        } else {
            return $response;
        }

        return $this;
    }

    /**
     * @param $id the user's id
     * @return object instance as user
     */
    public function getUser($id)
    {
        $response = $this->meli->request('GET', "/users/{$id}");
        if ($response['status'] == 200) {
            $this->fill($response['body']);
        } else {
            return $response;
        }

        return $this;
    }

    /**
     * @param $nickame the user's nickname
     * @return array
     */
    public function search($nickname)
    {
        $response = $this->meli->request('GET', 'search', ['query' => ['nickname' => $nickname]]);
        if ($response['status'] == 200) {
            return $response['body'];
        } else {
            return $response;
        }
    }

    public function __debugInfo()
    {
        $result = get_object_vars($this);
        unset($result['meli']);
        return $result;
    }

    /**
    * @param $data is an array containing data to be set in the object
    * @return object itself
    */
    private function fill(array $data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }
}