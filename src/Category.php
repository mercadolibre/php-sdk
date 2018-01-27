<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;

/**
 * Category
 */
class Category extends Resource
{
    /**
     * Initiates the object
     * 
     * @param object $meli as reference
     * @param array $data for filling the object
     */
	public function __construct(MeliRequestInterface &$meli, array $data = ['id' => ''])
	{
        parent::__construct($meli, $data, '/categories', true);

        if (
            isset($data['children_categories']) && 
            is_array($data['children_categories']) && 
            !empty($data['children_categories'])
        ) {
            $this->children_categories = array_map(function($item) {
                return new self($this->meli, $item);
            }, $data['children_categories']);
        }

        if (
            isset($data['path_from_root']) && 
            is_array($data['path_from_root']) && 
            !empty($data['path_from_root'])
        ) {
            $this->path_from_root = array_map(function($item) {
                return new self($this->meli, $item);
            }, $data['path_from_root']);
        }
	}

    /**
     * Get a category
     * 
     * @param string $id the category id
     * 
     * @throws InvalidArgumentException if the $id is null
     * @throws MeliException if the request was not successful
     * 
     * @return object instance of Category
     */
    public function getCategory($id)
    {
        $response = parent::getData($id);

        return new self($this->meli, $response);
    }

    /**
    * Get categories for a given country
    * 
    * @param string $country the country supported by MercadoLivre
    * @param bool $fully_load if must also request for fully data of every category
    * 
    * @throws InvalidArgumentException if the $country is not supported or both $country and $meli->current_country are empty
    * @throws MeliException if the $request was not successful
    * 
    * @return array of instances of Category
    */
    public function getCategories($country = '', $fully_load = true)
    {
        if (empty($country) && empty($this->meli->current_country)) {
            throw new InvalidArgumentException('You must select a country!');
        }

        if (!empty($country) && !in_array($country, $this->meli->supported_countries)) {
            $list = implode(', ', $this->meli->supported_countries);
            throw new InvalidArgumentException("You must select a valid country! Allowed values are: {$list}");
        }

        if (empty($country)) {
            $country = $this->meli->current_country;
        }

        $response = $this->meli->request('GET', "/sites/{$country}/categories", [], !$this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not get the categories!', $response);
        }


        $categories = [];

        foreach ($response['body'] as $category) {
            try {
                $cat = new self($this->meli, $category);

                if ($fully_load) {
                    $cat->load();
                }
            } catch (Exception $e) {
                $cat = $category;
            }

            array_push($categories, $cat);
        }

        return $categories;
    }

    /**
     * Searches for a category
     * 
     * @param string $category
     * 
     * @throws MeliException if the request was not successful
     * 
     * @return array containing the response
     */
    public function search($category)
    {
        $response = $this->meli->request('GET', 'search', ['query' => ['category' => $category]], !$this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not search for this category!', $response);
        }

        return $response['body'];
    }

    /**
     * Predict a category for one or more title
     * 
     * @param array|string $data containing either an array of titles to be predicted or a single title
     * 
     * @throws InvalidArgumentException if the $data is empty or doesn't contain a title
     * @throws MeliException if the request was not successful
     * 
     * @return array of instances of Category
     */
    public function predict($data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('You must pass at least one title!');
        }

        if (is_string($data)) {
            $data = [['title' => $data]];
        }

        $filtered = array_filter($data, function ($it) {
            return is_array($it) && isset($it['title']);
        });

        if (empty($filtered)) {
            throw new InvalidArgumentException('You must pass at least one title!');
        }

        $payload = [
            'json' => $filtered
        ];

        $response = $this->meli->request('POST', 'category_predictor/predict', $payload, !$this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not predict!', $response);
        }

        return array_map(function($predicted) {
            return new self($this->meli, $predicted);
        }, $response['body']);
    }
}