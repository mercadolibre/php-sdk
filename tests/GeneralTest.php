<?php
require 'src/meli.php';
class FirstTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        parent::setUp();
    }
    protected function getMeli() {
	        $smMock = $this->getMock('SessionManager', array('start'));
	        $smMock::staticExpects($this->any())->method('start')->will($this->returnValue(0));
			$meli = new Meli(array(
				'appId'  	=> 123456,
				'secret' 	=> "client secret",
				'mockUrl'	=> "http://localhost:3000",
			), $smMock);
			return $meli;
    }

    protected function tearDown()
    {
        
        parent::tearDown();
    }
		
	public function testGetAuthUrl ()
	{
		$meli = $this->getMeli();
		
		$this->assertEquals ("https://auth.mercadolibre.com.ar/authorization?client_id=123456&redirect_uri=http%3A%2F%2Fsomeurl.com&response_type=code", $meli->getLoginUrl (array('redirect_uri' => "http://someurl.com")));
	}

	public function  testAuthorizationSuccess ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("valid code with refresh token");
		$token = $meli->getAccessToken ();
		$this->assertEquals ("valid token", $token['value']);
		$this->assertEquals ("valid refresh token", $token['refresh_token']);
	}


	public function testAuthorizationFailure ()
	{
		$this->setExpectedException('AuthorizationException');
		$meli = $this->getMeli();
		$meli->initConnect("bad code");
		$token = $meli->getAccessToken ();
		$this->assertNull ($token['value']);
	}

	public function  testGet ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("valid code with refresh token");

		$sites = $meli->get ("/sites");
		$this->assertTrue(is_array($sites));
		$this->assertFalse(empty($sites));
	}

	public function testGetWithRefreshToken ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("expired code with refresh token");

		$resp = $meli->getWithAccessToken("/users/me");
		print_r($resp);
		$this->assertNotNull ($resp);
	}
	public function  testHandleErrors ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("invalid token");
		$resp = $meli->getWithAccessToken("/users/me");
		$this->assertEquals($resp['statusCode'], 403);

	}
	public function testPost ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("valid code with refresh token");

		$resp = $meli->postWithAccessToken ("/items", array('foo'=>'bar'));
		print_r($resp);
		$this->assertEquals(201, $resp['statusCode']);
	}

	public function  testPostWithRefreshToken ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("expired code with refresh token");

		$resp = $meli->postWithAccessToken ("/items", array('foo'=>'bar'));

		$this->assertEquals(201, $resp['statusCode']);
	}

	public function  testPut ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("valid code with refresh token");

		$resp = $meli -> putWithAccessToken  ("/items/123", array('foo'=>'bar'));

		$this->assertEquals(200, $resp['statusCode']);
	}
	public function  testPutWithRefreshToken ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("expired code with refresh token");

		$resp = $meli->putWithAccessToken ("/items/123", array('foo'=>'bar'));

		$this->assertEquals(200, $resp['statusCode']);
	}
	public function  testDelete ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("valid code with refresh token");

		$resp = $meli -> deleteWithAccessToken  ("/items/123");

		$this->assertEquals(200, $resp['statusCode']);
	}

	public function  testDeleteWithRefreshToken ()
	{
		$meli = $this->getMeli();
		$meli->initConnect("expired code with refresh token");

		$resp = $meli->deleteWithAccessToken ("/items/123");

		$this->assertEquals(200, $resp['statusCode']);
	}
  
}


?>