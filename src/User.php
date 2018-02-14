<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;

/**
 * User
 */
class User extends Resource
{
    /**
     * Initiates the object
     * 
     * @param object $meli as reference
     * @param array $data for filling the object
     */
    public function __construct(MeliRequestInterface &$meli, array $data = ['id' => ''])
    {
        parent::__construct($meli, $data, '/users', false);
    }

    /**
    * Gets the user itself
    * 
    * @throws MeliException if the request was not successful
    * 
    * @return object instance of User
    */
    public function getMe()
    {
        $response = parent::getData('me');

        return new self($this->meli, $response);
    }

    /**
    * Gets a user
    * 
    * @param string $id the user id
    * 
    * @throws InvalidArgumentException if the $id is null
    * @throws MeliException if the request was not successful
    * 
    * @return object instance of User
    */
    public function getUser($id)
    {
        $response = parent::getData($id);

        return new self($this->meli, $response);
    }

    /**
    * Searches for a user
    * 
    * @param $nickame the user's nickname
    * 
    * @throws InvalidArgumentException if the $nickname is null
    * @throws MeliException if the request was not successful
    * 
    * @return array containing the results
    */
    public function search($nickname)
    {
        if (is_null($nickname)) {
            throw new InvalidArgumentException('The nickname cannot be null!');
        }

        $response = $this->meli->request('GET', 'search', ['query' => ['nickname' => $nickname]], $this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not search for this user!', $response);
        }

        return $response['body'];
    }

    /**
    * Update the user's data
    * 
    * @param array $data to be updated
    * @param int $id, use the set data by default
    * 
    * @throws InvalidArgumentException if there's no valid ID set
    * @throws MeliException if the request was not successful
    * 
    * @return array containing the result
    */
    public function update(array $data, $id = false)
    {
        if ($id === false && (!isset($this->id) || empty($this->id))) {
            throw new InvalidArgumentException('You must set an id!');
        }

        if ($id === false) {
            $id = $this->id;            
        }

        $response = $this->meli->request('PUT', "/users/{$id}", ['json' => $data], $this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not update the user data!', $response);
        }

        return $response['body'];
    }

    /**
    * Creates a fake user
    * 
    * @param bool $short return a whole resource or just a short version of the resource, normally this differentiates from public(without need of access token) and private data
    * 
    * @return array
    */
    public static function fake($short = false)
    {
        $faker = parent::getFaker();

        $user = [
            'id' => $faker->randomNumber(7, true),
            'nickname' => $faker->userName,
            'registration_date' => $faker->iso8601,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'country_id' => $faker->countryCode,
            'email' => $faker->email,
            'identification' => [
                'type' => 'DNI',
                'number' => ''
            ],
            'address' => [
                'state' => $faker->state,
                'city' => $faker->city,
                'address' => $faker->streetAddress,
                'zip_code' => $faker->postcode,
            ],
            'phone' => [
                'area_code' => $faker->areaCode,
                'number' => $faker->cellphoneNumber,
                'extension' => '',
                'verified' => $faker->boolean(60),
            ],
            'alternative_phone' => [
                'area_code' => $faker->areaCode,
                'number' => $faker->cellphoneNumber,
                'extension' => '',
            ],
            'user_type' => 'real_estate_agency',
            'tags' => [
                'real_estate_agency',
                'test_user',
                'user_info_verified'
            ],
            'logo' => $faker->imageUrl(50, 50),
            'points' => $faker->randomNumber(3, true),
            'site_id' => $faker->country,
            'permalink' => $faker->url,
            'shipping_modes' => $faker->shipping_mode(false),
            'seller_experience' => $faker->seller_experience
        ];

        return $user;
    }
}