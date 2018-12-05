<?php
require_once '../MercadoLivre/meli.php';

class InitSDKTest extends PHPUnit_Framework_TestCase
{				

	protected static $meli;

	protected $client_id = '123';
    protected $client_secret = 'a secret';
    protected $redirect_uri = 'a redirect_uri';
    protected $access_token = 'a access_token';
    protected $refresh_token = 'a refresh_token';

    public function setUp() {

    	self::$meli = $this->getMock(
	          'Meli', array('execute'), array($this->client_id, $this->client_secret, $this->access_token, $this->refresh_token)
	        );

    }
    	#auth_url tests
		public function testGetAuthUrl() {

			$redirect_uri = self::$meli->getAuthUrl($this->redirect_uri);

			$this->assertEquals('https://auth.mercadolivre.com.br/authorization?client_id='.$this->client_id.'&response_type=code&redirect_uri='.urlencode($this->redirect_uri), $redirect_uri);

		}

		#auth tests
		public function testAuthorize() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/oauth/token'))
             ->will($this->returnCallback('getAuthorizeMock'));

			$reponse = self::$meli->authorize('a code', $this->redirect_uri);

			$this->assertEquals(200, $reponse['httpCode']);

			$reponse = self::$meli->authorize('a code', $this->redirect_uri);

			$this->assertEquals(400, $reponse['httpCode']);

		}

		public function testRefreshAccessToken() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/oauth/token'))
             ->will($this->returnCallback('getAuthorizeMock'));

			$reponse = self::$meli->refreshAccessToken();

			$this->assertEquals(200, $reponse['httpCode']);

			$reponse = self::$meli->refreshAccessToken();

			$this->assertEquals(400, $reponse['httpCode']);

			$this->refresh_token = null;
			self::$meli = $this->getMock(
	          'Meli', array('execute'), array($this->client_id, $this->client_secret, $this->access_token, $this->refresh_token)
	        );

			$reponse = self::$meli->refreshAccessToken();

			$this->assertEquals('Offline-Access is not allowed.', $reponse['error']);
		}

		#requests tests
		public function testGet() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/sites/MLB'))
             ->will($this->returnCallback('getSimpleCurl'));
	       	
	       	$reponse = self::$meli->get('/sites/MLB');

			$this->assertEquals(200, $reponse['httpCode']);

		}

		public function testPost() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/items'))
             ->will($this->returnCallback('getSimpleCurl'));

            $body = array(
            	"condition" => "new", 
            	"warranty" => "60 dias", 
            	"currency_id" => "BRL", 
            	"accepts_mercadopago" => true, 
            	"description" => "Lindo Ray_Ban_Original_Wayfarer", 
            	"listing_type_id" => "bronze", 
            	"title" => "oculos Ray Ban Aviador  Que Troca As Lentes  Lancamento!", 
            	"available_quantity" => 64, 
            	"price" => 289, 
            	"subtitle" => "Acompanha 3 Pares De Lentes!! Compra 100% Segura", 
            	"buying_mode" => "buy_it_now", 
            	"category_id" => "MLB5125", 
            	"pictures" => array(
            		array(
            			"source" => "http://upload.wikimedia.org/wikipedia/commons/f/fd/Ray_Ban_Original_Wayfarer.jpg"
            		), 
            		array(
            			"source" => "http://en.wikipedia.org/wiki/File:Teashades.gif"
            		)
            	)
            );

            $params = array('access_token' => $this->access_token);
	       	
	       	$reponse = self::$meli->post('/items', $body, $params);

			$this->assertEquals(200, $reponse['httpCode']);

		}

		public function testPut() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/items/MLB123'))
             ->will($this->returnCallback('getSimpleCurl'));

            $body = array(
            	"available_quantity" => 10, 
            	"price" => 280, 
            );

            $params = array('access_token' => $this->access_token);
	       	
	       	$reponse = self::$meli->put('/items/MLB123', $body, $params);

			$this->assertEquals(200, $reponse['httpCode']);

		}

		public function testDelete() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/questions/123'))
             ->will($this->returnCallback('getSimpleCurl'));

            $params = array('access_token' => $this->access_token);
	       	
	       	$reponse = self::$meli->delete('/questions/123', $params);

			$this->assertEquals(200, $reponse['httpCode']);

		}

		public function testOptions() {
			self::$meli->expects($this->any())
             ->method('execute')
             ->with($this->equalTo('/sites/MLB'))
             ->will($this->returnCallback('getSimpleCurl'));
	       	
	       	$reponse = self::$meli->options('/sites/MLB');

			$this->assertEquals(200, $reponse['httpCode']);

		}

		#makePath tests
		public function testMakePath() {
			$params = array(
				'access_token' => 'a access_token',
				'ids' => 'MLB123,MLB321'
			);

	       	$reponse = self::$meli->make_path('/items', $params);
			
			$this->assertEquals('https://api.mercadolibre.com/items?access_token=a access_token&ids=MLB123,MLB321', $reponse);

			$reponse = self::$meli->make_path('items', $params);
			
			$this->assertEquals('https://api.mercadolibre.com/items?access_token=a access_token&ids=MLB123,MLB321', $reponse);

			// $reponse = self::$meli->make_path('https://api.mercadolibre.com/items', $params);
			
			// $this->assertEquals('https://api.mercadolibre.com/items?access_token=a access_token&ids=MLB123,MLB321', $reponse);
			
		}

		
    public function tearDown() {
		parent::tearDown();
    }
}


#Mock requests
$code = 0;
function getAuthorizeMock() {
	global $code;
	$code++;

	if($code == 1) {
		$body = array(
			'access_token' => 'a access_token', 
			'token_type' => 'bearer',
			'expires_in' => '10800',
			'scope' => 'offline_access read write',
			'refresh_token' => 'a refresh_token' 
		);
		$return['body'] = (object) $body;
		$return['httpCode'] = 200;
	} else {
		$body = array(
			'message' => 'Error validating grant. Your authorization code or refresh token may be expired or it was already used.',
			'error' => 'invalid_grant',
			'status' => 400,
			'cause' => array()
		);
		$return['body'] = (object) $body;
		$return['httpCode'] = 400;
	}
	
	return $return;
}


$refresh = 0;
function getRefreshTokenMock() {
	global $refresh;
	$refresh++;

	if($refresh == 1) {
		$body = array(
			'access_token' => 'a access_token', 
			'token_type' => 'bearer',
			'expires_in' => '10800',
			'scope' => 'offline_access read write',
			'refresh_token' => 'a refresh_token' 
		);
		$return['body'] = (object) $body;
		$return['httpCode'] = 200;
	} else if($refresh == 2) {
		$body = array(
			'message' => 'Error validating grant. Your authorization code or refresh token may be expired or it was already used.',
			'error' => 'invalid_grant',
			'status' => 400,
			'cause' => array()
		);
		$return['body'] = (object) $body;
		$return['httpCode'] = 400;
	} else {
		$body = array(
			'message' => 'Error validating grant. Your authorization code or refresh token may be expired or it was already used.',
			'error' => 'invalid_grant',
			'status' => 400,
			'cause' => array()
		);
		$return['body'] = (object) $body;
		$return['httpCode'] = 400;
	}
	
	return $return;
}

function getSimpleCurl() {
	$return['body'] = 'null';
	$return['httpCode'] = 200;

	return $return;
}