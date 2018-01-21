<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;

/**
 * User
 */
class User
{
    /**
    * @var object $meli instance for making requests
    */
    private $meli;

    /**
     * Receives a Meli instance as reference for making requests
     * @author Matheus Hernandes {github.com/onhernandes}
     * @return $this
     */
	public function __construct(Meli &$meli, array $data = [])
	{
        $this->meli = $meli;
        $this->fill($data);
        return $this;
	}

    /**
    * Gets the user itself
    * @return an instance of the user
    */
    public function getMe()
    {
        $response = $this->meli->request('GET', '/users/me');

        if ($response['status'] == 200) {
            $this->fill($response['body']);
            return $this;
        } else {
            throw new Exception('Could not get this user!');
        }
    }

    /**
     * @param int $user_id the user's id
     * @return object instance as user
     */
    public function getUser($user_id)
    {
        $response = $this->meli->request('GET', "/users/{$user_id}");

        if ($response['status'] == 200) {
            $this->fill($response['body']);
            return $this;
        } else {
            throw new Exception('Could not get this user!');
        }
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

    /**
    * Update the user's data
    * 
    * @param array $data
    * @param int $user_id, use the set data by default
    * @return mixed
    */
    public function update(array $data, $user_id = false)
    {
        if ($user_id === false && (!isset($this->id) || empty($this->id))) {
            throw new InvalidArgumentException('You must set an user_id!');
        }

        if ($user_id === false) {
            $user_id = $this->id;            
        }

        return $response = $this->meli->request('PUT', "/users/{$user_id}", ['json' => $data], true);
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