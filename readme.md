MeliPHP unofficial MercadoLibre PHP SDK (v.0.0.1)
==========================

This repository contains the open source MeliPHP Unofficial PHP SDK that allows you to access MercadoLibre Platform from your PHP app. 
MeliPHP is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Create your app
---------------

	http://en.mercadolibre.io/aplicaciones

Examples
--------
	
[example_login][example_login]
	
	$meli = new Meli(array(
		'countryId' => 'ar',
		'appId'  	=> '11111',
		'secret' 	=> 'kKoqU3tvm9sw2NXx5EnhmPM4xzg3M08HFzrBU',
	));
	
	$userId = $meli->initConnect();
	
	// Login or logout url will be needed depending on current user state.
	if ($userId) {
	  $user = $meli->getWithAccessToken('/users/me');
	}

	
[example_search][example_search]
		
	$meli = new Meli(array(
		'countryId' => 'ar',
		'appId'  	=> '11111',
		'secret' 	=> 'kKoqU3tvm9sw2NXx5EnhmPM4xzg3M08HFzrBU',
	));


	$search = $meli->get('/sites/#{siteId}/search',array(
	'q' => $query,
	));

[example_login]: http://github.com/foocoders/meli-php/blob/master/examples/example_login.php
[example_search]: http://github.com/foocoders/meli-php/blob/master/examples/example_search.php
