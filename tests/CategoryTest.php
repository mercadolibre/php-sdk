<?php 

use PHPUnit\Framework\TestCase;
use \Meli\Meli;
use \Meli\Category;

class CategoryTest extends TestCase
{
    private $meli;
	private $category;

    /**
    * Initiates the object with Meli and Category. Since categories are public and don't require access token, we don't need them here.
    */
    public function __construct()
    {
    	$this->meli = new Meli('MLB', []);
        $this->category = new Category($this->meli);
    }

    /**
    * Test Category->getCategory('MLB163702');
    */
    public function testGetCategory()
    {
        $this->assertInstanceOf(
            Category::class, 
            $this->category->getCategory('MLB163702')
        );
    }

    /**
    * Test Category->getCategories();
    */
    public function testGetCategories()
    {
        $this->assertContainsOnlyInstancesOf(Category::class, $this->category->getCategories());
    }

    /**
    * Test Category->predict();
    */
    public function testPredict()
    {
        $this->assertContainsOnlyInstancesOf(Category::class, $this->category->predict('Iphone 5 8GB'));
    }

    /**
    * Test Category->search('MLB163702');
    */
    public function testSearch()
    {
        $this->assertArrayHasKey('results', $this->category->search('MLB163702'));
    }
}