<?php

namespace Meli;

/**
 * Item
 */
class Item extends Resource
{
    /** @var bool If must or not validate also types, not only the keys. Like for category, will instantiate Category for checking if the category exists */
    public $post_checking = true;

    /** @var array Mandatory attributes */
    private $general_mandatory_attributes = [
        'title' => '', 
        'description' => '', 
        'condition' => ['new', 'used', 'not_specified'],
        'available_quantity' => '',
        'category_id' => '',
        'price' => '',
        'currency_id' => '',
        'buying_mode' => '',
        'listing_type_id' => '',
    ];

    /**
     * Initiates the object
     * 
     * @param object $meli as reference
     * @param array $data for filling the object
     */
    public function __construct(Meli &$meli, array $data = ['id' => ''])
    {
        parent::__construct($meli, $data, '/items', true);
        $this->fill($data);
    }

    /**
    * Gets a product
    * 
    * @param string $id the product id
    * 
    * @throws InvalidArgumentException if the $id is null
    * @throws MeliException if the request was not successful
    * 
    * @return object instance of Item
    */
    public function getItem($id)
    {
        $response = parent::getData($id);

        return new self($this, $response);
    }

    /**
    * Create an item
    * 
    * @param array|object $item data
    * @param bool $post_checking if must check also for types within Item, this can take more time because may need to request more data from ML
    * 
    * @throws InvalidArgumentException if any field is invalid
    * @throws MeliException if the request was not successful
    * 
    * @return array of response from MercadoLivre
    */
    public function createItem($item, $post_checking = true)
    {
        $errors = [];
        $new = new self($this->meli, $item);

        if (is_object($item)) {
            $item = (array) $item;
        }

        $new->validateGeneral($errors);

        if ($post_checking) {
            $new->validateCategory($errors);
        }

        if (!empty($errors)) {
            throw new MeliException('Invalid item!', $errors);
        }

        $response = $this->meli->request('POST', '/items/', ['json' => $item]);

        if ($response['status'] == 200) {
            return $response;
        } else {
            throw new Exception('Could not get this item!');
        }
    }

    /**
    * Validates item general attributes
    * 
    * @param array $errors accepts an array by reference for user friendly error message 
    * 
    * @return bool
    */
    public function validateGeneral(array &$errors = [])
    {
        $local = [];

        foreach ($this->general_mandatory_attributes as $k => $v) {
            if (
                !property_exists($this, $k) || 
                $this->$k == '' || 
                $this->$k == false || 
                is_null($this->$k)
            ) {
                $errors[$k] = $local[$k] = "Either {$k} is missing or is empty('', false or null are not allowed values)!";
                continue;
            }

            if (is_array($v) && !in_array($this->$k, $v)) {
                $valid = implode(', ', $v);
                $errors[$k] = $local[$k] = "{$this->$k} is not a valid value! Valid values are {$valid}";
            }
        }

        return empty($local);
    }

    /**
    * Validates item category and related
    * 
    * @param array $errors accepts an array by reference for user friendly error message 
    * 
    * @return bool
    */
    public function validateCategory(array &$errors = [])
    {
        if (!property_exists($this, 'category_id') || empty($this->category_id)) {
            $errors['category_id'] = 'Invalid category!';
            return false;
        }

        if (!is_object($this->category_id)) {
            $this->category_id = new Category($this->meli, $this->category_id);
        }


        if (property_exists($this, 'buying_mode') && !in_array($this->buying_mode, $this->category_id->settings['buying_modes'])) {
            $errors['buying_mode'] = 'This buying_mode is invalid! Accepted values for this category are: '.implode(', ', $this->category_id->settings['buying_modes']);
        }

        if (property_exists($this, 'currency_id') && !in_array($this->currency_id, $this->category_id->settings['currencies'])) {
            $errors['currency_id'] = 'This currency_id is invalid! Accepted values for this category are: '.implode(', ', $this->category_id->settings['currencies']);
        }

        if (property_exists($this, 'pictures') && count($this->pictures) > $this->category_id->settings['max_pictures_per_item']) {
            $this->pictures = array_slice($this->pictures, 0, count($this->pictures) - $this->category_id->settings['max_pictures_per_item']);
        }

        if (property_exists($this, 'condition') && !in_array($this->condition, $this->category_id->settings['item_conditions'])) {
            $errors['condition'] = 'This condition is invalid for this category! Accepted values for this condition are: '.implode(', ', $this->category_id->settings['item_conditions']);
        }

        if (isset($item['shipping_mode'])) {
            $user = new User($this->meli);
            $user->load();

            if (property_exists($user, 'shipping_modes' && is_array($user->shipping_modes) && !in_array($item['shipping_mode'], $user->shipping_modes))) {
                $errors['shipping_mode'] = 'This shipping_mode is not allowed! Accepted values are: '.implode(', ', $user->shipping_modes);
            }
        }
    }

    /**
    * Validates item shipping and related
    * 
    * @param array $errors accepts an array by reference for user friendly error message 
    * 
    * @return bool
    */
    public function validateShipping(array &$errors = [])
    {
        // Some cool code will born here
    }

    /**
    * Validates item pictures
    * 
    * @param array $errors accepts an array by reference for user friendly error message 
    * 
    * @return bool
    */
    public function validatePictures(array &$errors = [])
    {
        // Some cool code will born here
    }

    /**
    * Validates item listing type and related
    * 
    * @param array $errors accepts an array by reference for user friendly error message 
    * 
    * @return bool
    */
    public function validateListingType(array &$errors = [])
    {
        // Some cool code will born here
    }

    /**
    * Gets a list of products
    * 
    * @param int $page current page
    * 
    * @throws InvalidArgumentException if any field is invalid
    * @throws MeliException if the request was not successful
    * 
    * @return array of items from MercadoLivre
    */
    public function getItems($page = 0)
    {
        // Some cool code will born here
    }
}