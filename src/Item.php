<?php

namespace Meli;

/**
 * Item
 */
class Item implements MeliInterface
{
    private $meli;

    /**
     * If must or not validate also types, not only the keys. 
     * Like for category, will instantiate Category for checking if the category exists
     *
     * @var bool
     */
    public $post_checking = true;

    /**
     * Mandatory attributes
     *
     * @var array
     */
    private $general_mandatory_attributes = [
        'title' => '', 
        'description' => '', 
        'condition' => ['new', 'used', 'not_specified'],
        'available_quantity' => '',
        'category_id' => '',
        'price' => '',
        'currency_id' => '',
        'buying_mode' => '',
        'listing_type' => '',
    ];

    /**
     * Receives a Meli instance as reference for making requests
     * 
     * @param object $meli as reference
     * @param array $data
     * @return void
     */
    public function __construct(Meli &$meli, array $data = [])
    {
        $this->meli = $meli;
        $this->fill($data);
    }

    /**
    * Gets a product based on product's id
    * @param string $id is the product's id
    * @return an instance of Item or throws an error
    */
    public function getItem($id)
    {
        $response = $this->meli->request('GET', "/items/{$id}");

        if ($response['status'] == 200) {
            return new self($this, $response['body']);
        } else {
            throw new Exception('Could not get this item!');
        }
    }

    /**
    * Create an item
    * 
    * @param array $item data
    * @return mixed
    */
    public function createItem($item)
    {
        $response = $this->meli->request('POST', '/items/', ['json' => $item]);

        if ($response['status'] == 200) {
            return $response;
        } else {
            throw new Exception('Could not get this item!');
        }
    }

    /**
    * Gets a list of products
    * @param $page is the current page
    * @return an array with the result, empty in case of any product's found or throws an instance of MeliException
    */
    public function getItemList($page = 0)
    {
        // Some cool code will born here
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
     * summary
     *
     * @return void
     * @author 
     */
    public function validate()
    {
        $errors = [];

        foreach ($this->general_mandatory_attributes as $key => $value) {
            if (!property_exists($this, $key)) {
                $errors[$key] = "{$this->$key} is mandatory, must be set!";
            }

            if (is_null($this->$key) || $this->$key == '') {
                $errors[$key] = "{$this->$key} is mandatory and can not be null or empty string!";
            }
        }

        if (!$this->post_checking) {
            throw new Exception('Invalid item! See data for more info about the errors!');
        }

        if (!in_array($this->condition, $this->general_mandatory_attributes['condition'])) {
            $allowed_conditions = implode(', ', $this->general_mandatory_attributes['condition']);
            $errors['condition'] = "The value {$this->condition} is not valid, you must one of the following values: {$allowed_conditions}";
        }

        // Need to change static methods, its too tricky using them. 

        if (!Category::validate($this->category_id)) {
            $errors['category_id'] = 'The current category_id does not exists, please submit a new one!';
        }

        if (!Category::validate($this->category_id, 'buying_modes', $this->buying_mode)) {
            $errors['buying_mode'] = 'The current buying_mode does not exists, please submit a new one!';
        }

        if (!Currency::validate($this->currency_id)) {
            $errors['currency_id'] = 'The current currency_id does not exists, please submit a new one!';
        }

        if (!ListingType::validate($this->listing_type)) {
            $errors['listing_type'] = 'The current listing_type does not exists, please submit a new one!';
        }

        if (!ListingType::validate($this->listing_type, 'max_stock_per_item', $this->available_quantity)) {
        }

        // ListingType -> Validate for 'listing_type'
        // ListingType -> Validate for 'requires_picture'
        // ListingType -> Validate for 'not_available_in_categories'
        // ListingType -> Validate for 'immediate_payment'
        // ListingType -> Validate for 'max_stock_per_item'
        // Category -> Validate for 'category_id'
        // Category -> Validate for 'currencies'
        // Category -> Validate for 'shipping_modes'
        // Category -> Validate for 'shipping_options'
        // Category -> Validate for 'shipping_profile'
        // Category -> Validate for 'immediate_payment'
        // Category -> Validate for 'item_conditions'
        // Category -> Validate for 'max_pictures_per_item'
    }

    /**
    * Validate images for MercadoLivre
    * 
    * @param array $images
    * @return array
    */
    public static function validateImages(array $images)
    {

        for ($i = 0; $i < count($images); $i++) {
            if (!isset($img[$i]['source']) || empty($img[$i]['source'])) {
                throw new Exception("Image source is empty! Index: {$i}");
            }
        }

        return true;
    }
}