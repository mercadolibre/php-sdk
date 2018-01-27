<?php

namespace Meli\MeliException;

/**
 * MeliException - throw custom Exception
 */
class MeliException extends Exception
{
	/** @var mixed Contains the Exception data */
	private $data;

	/**
	* Initiates the object
	* 
	* @param string $message Exception's message
	* @param mixed $data Exception's data
	* @param int $code Exception's code
	*/
	public function __construct($message = '', $data = [], $code = 0)
	{
		parent::__construct($message, $code);
		$this->data = $data;
	}

	/**
	* Gets the $data
	* 
	* @return mixed $data
	*/
	public function getData()
	{
		return $this->data;
	}
}