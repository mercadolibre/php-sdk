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
	public function __construct(Meli &$meli, array $data = [])
	{
        $this->meli = $meli;
        $this->fill($data);

        if (isset($data['children_categories']) && is_array($data['children_categories']) && !empty($data['children_categories'])) {
            $this->children_categories = array_map(function($item) {
                return new self($this->meli, $item);
            }, $data['children_categories']);
        }

        return $this;
	}

    /**
     * Get a category
     * 
     * @param string $id
     * @return void
     */
    public function getCategory($id)
    {
        $response = $this->request->request('GET', "/categories/{$id}");

        if ($response['status'] !== 200) {
            throw new Exception('Could not get this category!');
        }

        return new self($this->meli, $response['body']);
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

    /**
    * Try to get data for this category itself and load in the object, because not always all the category data are loaded in the first state.
    * 
    * @return $this
    */
    public function load()
    {
        $response = $this->meli->request('GET', "/categories/{$category_id}");

        if ($response['status'] !== 200) {
            throw new Exception('Could not get this category!');
        }

        $this->fill($response['body']);
        return $this;
    }
}