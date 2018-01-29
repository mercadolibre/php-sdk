<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;
use \Faker\Factory;

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

    /**
    * Creates a fake category
    * 
    * @param bool $short return a whole category or just name and id
    * 
    * @return array
    */
    public static function fake($short = false)
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\Internet($faker));
        $faker->addProvider(new \Faker\Provider\Miscellaneous($faker));
        $faker->addProvider(new MeliFakeProvider($faker));

        if ($short) {
            return [
                'id' => $faker->id(true),
                'name' => $faker->category
            ];
        }

        $category = [
            'id' => $faker->id,
            'name' => $faker->category,
            'shipping_mode' => $faker->shipping_mode,
            'picture' => $faker->imageUrl(640, 480),
            'permalink' => $faker->url,
            'total_items_in_this_category' => $faker->randomNumber(),
            'path_from_root' => array_map(function() {
                    return self::fake(true);
                }, array_pad([], $faker->numberBetween(1, 5), 10)),
            'children_categories' => array_map(function() {
                    return self::fake(true);
                }, array_pad([], $faker->numberBetween(1, 5), 10)),
            'settings' => [
                'adult_content' => $faker->boolean(10),
                'buying_allowed' => $faker->boolean(99),
                'buying_modes' => $faker->buying_mode(false),
                'catalog_domain' => $faker->url,
                'coverage_areas' => '',
                'currencies' => $faker->currency,
                'fragile' => $faker->boolean(5),
                'immediate_payment' => 'required',
                'item_conditions' => $faker->item_condition(false),
                'items_reviews_allowed' => $faker->boolean(30),
                'listing_allowed' => $faker->boolean(90),
                'max_description_length' => $faker->numberBetween(30000, 50000),
                'max_pictures_per_item' => $faker->numberBetween(1, 15),
                'max_pictures_per_item_var' => $faker->numberBetween(1, 13),
                'max_sub_title_length' => $faker->numberBetween(1, 70),
                'max_title_length' => $faker->numberBetween(1, 60),
                'maximum_price' => $faker->numberBetween(1, 9999999),
                'minimum_price' => 0,
                'mirror_category' => null,
                'mirror_master_category' => null,
                'mirror_slave_categories' => [],
                'price' => 'required',
                'reservation_allowed' => null,
                'restrictions' => [],
                'rounded_address' => false,
                'seller_contact' => 'not_allowed',
                'shipping_modes' => $faker->shipping_mode(true),
                'shipping_options' => $faker->shipping_option(true),
                'shipping_profile' => 'shipping_profile',
                'show_contact_information' => false,
                'simple_shipping' => 'optional',
                'stock' => 'required',
                'sub_vertical' => null,
                'subscribable' => null,
                'tags' => ['others'],
                'vertical' => null,
                'vip_subdomain' => 'produto'
            ],
            'attribute_types' => 'attributes',
            'meta_categ_id' => $faker->id
        ];

        return $category;
    }
}