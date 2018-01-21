<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;

/**
 * Category
 */
class Category
{
    /**
    * @var object $meli instance for making requests
    */
    private $meli;

    /**
     * Receives a Meli instance as reference for making requests
     * @param object as reference Meli $meli
     * @param array $data
     * @param boolean $autoload
     * @return $this
     */
	public function __construct(Meli &$meli, array $data = [], $autoload = true)
	{
        $this->meli = $meli;
        $this->fill($data);

        if ($autoload) {
            try {
                $loaded = $this->getCategory($this->id);
                $this->fill($loaded);
            } catch (Exception $e) {
                // Some prevention will born here
            }
        }

        return $this;
	}

    public function getCategory($id)
    {
        $response = $this->request->request('GET', "/categories/{$id}");

        if ($response['status'] !== 200) {
            throw new Exception('Could not get this category!');
        }

        return $response['body'];
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