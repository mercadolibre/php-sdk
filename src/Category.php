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
     * Searches for a category
     * 
     * @param string $category
     * @return mixed
     */
    public function search($category)
    {
        $response = $this->meli->request('GET', 'search', ['query' => ['category' => $category]]);

        if ($response['status'] !== 200) {
            throw new Exception('Could not search for this category!');
        }

        return $response['body'];
    }

    /**
     * Predict a category for a title
     *
     * @param string $title
     * @return mixed
     */
    public function predictOne($title)
    {
        $response = $this->meli->request('GET', 'category_predictor/predict', ['query' => [
                'title' => $title
            ]]);

        if ($response['status'] !== 200) {
            throw new Exception('Could not predict!');
        }

        return $response['body'];
    }

    /**
     * Predict a category for 
     *
     * @param array $data containing arrays with at least a 'title' index. 'category' index is optional
     * @return mixed
     */
    public function predict(array $data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('You must pass at least one title!');
        }

        $filtered = array_filter($data, function ($it) {
            return is_array($it) && isset($it['title']);
        });

        if (empty($filtered)) {
            throw new InvalidArgumentException('You must pass at least one title!');
        }

        if (count($filtered) == 1) {
            $method = 'GET';
            $payload = [
                'query' => [
                    'title' => $filtered[0]['title']
                ]
            ];
        } else {
            $method = 'POST';
            $mapped = array_map(function($it) {
                $result = [
                    'title' => $it['title']
                ];

                if (isset($it['category_from'])) {
                    $result['category_from'] = $it['category_from'];
                }

                return $result;
            }, $filtered);

            $payload = [
                'json' => $mapped
            ];
        }

        $response = $this->meli->request($method, 'category_predictor/predict', $payload);

        if ($response['status'] !== 200) {
            throw new Exception('Could not predict!');
        }

        return $response['body'];
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