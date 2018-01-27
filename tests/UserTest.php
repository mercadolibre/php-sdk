<?php 

use PHPUnit\Framework\TestCase;
use \Meli\Meli;
use \Meli\User;

class UserTest extends TestCase
{
    private $meli;
	private $category;

    /**
    * Initiates the object with Meli and User. Since categories are public and don't require access token, we don't need them here.
    */
    public function __construct()
    {
    	$this->meli = new Meli('MLB', []);
        $this->category = new User($this->meli);
    }
}