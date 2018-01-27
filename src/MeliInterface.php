<?php 

namespace Meli;


interface MeliInterface
{
	/**
	* Fills the object itself with an array of data
	* 
	* @param array $data
	* @return object $this
	*/
    public function fill(array $data = []);

	/**
	* Removes $meli from debugging
	* 
	* @return array
	*/
    public function __debugInfo();

	/**
	* Validates the current object or the specified $field and $value
	* 
	* @param string $field
	* @param string $value
	* @return bool
	*/
    public function validate($field = '', $value = '');

	/**
	* Gets data for the current object from ML's API
	* 
	* @param string $id
	* @return array|throws Exception
	*/
    public function getData($id);

	/**
	* Gets data for the current object from ML's API and load in itself instead returning a new instance
	* 
	* @return object $this|throws Exception
	*/
    public function load();
}