MeliPHP unofficial MercadoLibre PHP SDK (v.0.0.1)
==========================

This repository contains the open source MeliPHP Unofficial PHP SDK that allows you to access MercadoLibre Platform from your PHP app. 
MeliPHP is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

The [example_login][example_login]

$meli = new Meli(array(
	'countryId' => 'ar',
	'appId'  	=> '4459',
	'secret' 	=> 'kKoqUtvm9NXx5EnhmPM4xzgM08HFzrBU',
));

$userId = $meli->getUserId();

// Login or logout url will be needed depending on current user state.
if ($userId) {
  $user = $meli->get(true,'/users/me');
}

The [example_search][example_search]

	    

$meli = new Meli(array(
	'countryId' => 'ar',
	'appId'  	=> '344617158898614',
	'secret' 	=> '6dc8ac871858b34798bc2488200e503d',
));

	$search = $meli->get(false,'/sites/#{siteId}/search',array(
	'q' => 'mp3',
	));

[example_login]: http://github.com/foocoders/meli-php/blob/master/examples/example_login.php
[example_search]: http://github.com/foocoders/meli-php/blob/master/examples/example_search.php
