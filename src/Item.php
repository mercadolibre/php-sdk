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
    public function createItem($item, $post_checking = true, $user = null)
    {
        $errors = [];

        if (is_object($item)) {
            $item = (array) $item;
        }

        foreach ($this->general_mandatory_attributes as $k => $v) {
            if (!isset($item[$k]) || $item[$k] == '' || $item[$k] == false || is_null($item[$k])) {
                $errors[$k] = "Either {$k} is missing or is empty('', false or null are not allowed values)!";
                continue;
            }

            if (is_array($v) && !in_array($item[$k], $v)) {
                $valid = implode(', ', $v);
                $errors[$k] = "{$item[$k]} is not a valid value! Valid values are {$valid}";
            }
        }

        if ($post_checking) {
            if (!isset($errors['category_id'])) {
                if (!is_object($item['category_id'])) {
                    $item['category_id'] = new Category($this->meli, $item['category_id']);
                }

                if (!$item['category_id']->validate()) {
                    $errors['category_id'] = 'Invalid category!';
                }
            }

            if (!isset($errors['category_id'])) {
                if (!isset($errors['buying_mode']) && !in_array($item['buying_mode'], $item['category_id']->settings['buying_modes'])) {
                    $errors['buying_mode'] = 'This buying_mode is invalid! Accepted values for this category are: '.implode(', ', $item['category_id']->settings['buying_modes']);
                }

                if (!isset($errors['currency_id']) && !in_array($item['currency_id'], $item['category_id']->settings['currencies'])) {
                    $errors['currency_id'] = 'This currency_id is invalid! Accepted values for this category are: '.implode(', ', $item['category_id']->settings['currencies']);
                }

                if (isset($item['pictures']) && count($item['pictures']) > $item['category_id']->settings['max_pictures_per_item']) {
                    $item['pictures'] = array_slice($item['pictures'], 0, count($item['pictures']) - $item['category_id']->settings['max_pictures_per_item']);
                }

                if (!isset($errors['condition']) && !in_array($item['condition'], $item['category_id']->settings['item_conditions'])) {
                    $errors['condition'] = 'This condition is invalid for this category! Accepted values for this condition are: '.implode(', ', $item['category_id']->settings['item_conditions']);
                }
            }
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

    /**
     * summary
     *
     * @return void
     * @author 
     */
    public function validate()
    {
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
        // Category -> Validate for 'shipping_modes'
        // Category -> Validate for 'shipping_options'
        // Category -> Validate for 'shipping_profile'
        // Category -> Validate for 'immediate_payment'
    }
}