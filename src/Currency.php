<?php

namespace Meli;

use \Exception;
use \InvalidArgumentException;

/**
 * Currency
 */
class Currency
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
        return $this;
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

        if (isset($this->path_from_root) && is_array($this->path_from_root) && !empty($this->path_from_root)) {
            $this->path_from_root = array_map(function($path) {
                return new self($this->meli, $value);
            }, $this->path_from_root);
        }

        return $this;
    }
}