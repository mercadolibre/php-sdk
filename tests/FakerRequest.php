<?php 

/**
 * FakerRequest creates fake requests 
 */
class FakerRequest implements \Meli\MeliRequestInterface
{
    /**
     * Faker
     *
     * @var string
     */
    private $faker;

    /**
    * Initiates the object
    * 
    * @param object $resource is a resource
    */
    public function __construct(Resource $resource)
    {
        $this->faker = $resource;
    }

    /**
    * Fake request
    * 
    * @param string $method for HTTP Method 
    * @param string $uri for URI 
    * @param array $data to be sent within request 
    * @param bool $append_access_token if must append the access token within the request
    * 
    * @return array with body, status and headers
    */
    public function request($method, $uri, array $data = [], $append_access_token = false)
    {
        return [
            'body' => $this->faker::fake(),
            'status' => 200,
            'headers' => []
        ];
    }
}