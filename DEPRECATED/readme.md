MeliPHP official MercadoLibre PHP SDK (v.0.0.4)
==========================

This repository contains the open source MeliPHP Unofficial PHP SDK that allows you to access MercadoLibre Platform from your PHP app. 
MeliPHP is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

You need sign up your application [http://applications.mercadolibre.com/](http://applications.mercadolibre.com/)

		// Try to put this code at the top
		require '/path/to/your/src/meli.php';
	
		// Create our Application instance (replace this with your appId and secret).
		$meli = new Meli(array(
			'appId'  		=> '12345',
			'secret' 		=> 'dsadsaDWFfs24DF34dgg43T3',
		));


Examples
--------

* Login with MercadoLibre
		
		$userId = $meli->initConnect();
		
		if ($userId) {
		  $user = $meli->getWithAccessToken('/users/me');
		}

	[code](https://github.com/mercadolibre/php-sdk/blob/master/examples/example_login.php)

* Search items
 	
		$query = $_REQUEST['q'];
	
		$search = $meli->get('/sites/#{siteId}/search',array(
			'q' => $query,
		));
	
	[code](https://github.com/mercadolibre/php-sdk/blob/master/examples/example_search.php)

=======
* View item

	 	$itemId = $_REQUEST['item_id'];
	
	    $item = $meli -> get('/items/' . $itemId);

	[code](https://github.com/mercadolibre/php-sdk/blob/master/examples/example_item.php)

=======
* Questions

		$user = $meli -> getWithAccessToken('/users/me');
		
		$unansweredQuestions = $meli -> getWithAccessToken('/questions/search', array('seller' => $user['id'], 'status' => 'UNANSWERED'));


	[code](https://github.com/mercadolibre/php-sdk/blob/master/examples/example_questions.php)

=======
* Get the Access Token

		$userId = $meli->initConnect();
		
		if ($userId) {
		  $accessToken = $meli->getAccessToken();
		}