<?php 

namespace Meli;

/**
 * Resource for Meli
 */
class Resource implements MeliInterface
{
	/** @var object An instance of any object with implements MeliRequestInterface */
	protected $meli;

    /** @var string Endpoint for the request */
    protected $endpoint;

	/** @var bool Public resources don't need access token on request */
	protected $is_public_resource;

	/** @var string|int Current resource ID */
	public $id;

    /**
     * Initiates the object
     * 
     * @param object $meli as reference
     * @param array $data for filling the object
     * @param string $endpoint for making requests later
     * 
     * @throws InvalidArgumentException if the $data does not has the ID index!
     */
    public function __construct(MeliRequestInterface &$meli, array $data = ['id' => ''], $endpoint, $is_public_resource = false)
    {
        $this->endpoint = $endpoint;
    	$this->is_public_resource = $is_public_resource;
        $this->meli = $meli;

        if (!isset($data['id'])) {
        	throw new InvalidArgumentException('You must pass at least an id index!');
        }

        $this->fill($data);
    }

    /**
    * Gets data for the current object from ML's API
    * 
    * @param string $id ID for request
    * @param bool $append_access_token if must append the access_token in the URL or not
    * 
    * @throws InvalidArgumentException if the $id is null
    * @throws MeliException if the $request was not successful
    * 
    * @return array
    */
    public function getData($id)
    {
    	if (is_null($id)) {
    		throw new InvalidArgumentException('The id can not be null!');
    	}

        $response = $this->meli->request('GET', $this->endpoint.'/'.$id, [], !$this->is_public_resource);

        if ($response['status'] !== 200) {
            throw new MeliException('Could not get the data you wanted!', $response);
        }

        return $response['body'];
    }

	/**
	* Fills the object itself with an array of data
	* 
	* @param array $data for filling the object
    * 
	* @return object $this the object itself
	*/
    public function fill(array $data = [])
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }

    /**
    * Try to get data for this resource and load in the object
    * 
    * @param bool $append_access_token if must append the access_token in the URL or not
    * 
    * @throws InvalidArgumentException if the $id is null
    * @throws MeliException if the $request was not successful
    * 
    * @return object $this the object itself
    */
    public function load()
    {
        $data = $this->getData($this->id);
        $this->fill($data);
        return $this;
    }

    /**
    * Validates the current object or the specified $field and $value
    * 
    * @param string $field for checking if exists
    * @param string $value for checking if it is equal
    * 
    * @throws MeliException if the $request was not successful when loading the object data from source
    * 
    * @return bool for success or error
    */
    public function validate($field = '', $value = '') {
        try {
            if (empty($field) || !property_exists($this, $field)) {
                $this->load();
            }

            if (empty($field)) {
                return true;
            }

            if (!property_exists($this, $field)) {
                return false;
            }

            if (is_array($this->$field) && !is_array($value)) {
                return in_array($value, $this->$field);
            }

            return $this->$field == $value;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
    * Remove $meli from debug functions
    * 
    * @return array containing the object values
    */
    public function __debugInfo()
    {
        $result = get_object_vars($this);
        unset($result['meli']);
        unset($result['endpoint']);
        unset($result['is_public_resource']);
        return $result;
    }
}