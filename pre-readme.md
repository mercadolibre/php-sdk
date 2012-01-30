MeliPHP unofficial MercadoLibre PHP SDK (v.0.0.1)
==========================

This repository contains the open source MeliPHP Unofficial PHP SDK that allows you to access MercadoLibre Platform from your PHP app. 
MeliPHP is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

The [examples][examples] are a good place to start. The minimal you'll need to
have is:

    require 'meli-php-sdk/src/meli.php';

    $meli = new Meli(array(
      'country'	=> 'YOUR_COUNTRY_ID',
      'appId'  	=> 'YOUR_APP_ID',
      'secret' 	=> 'YOUR_APP_SECRET',
    ));

    // Search mp3
	$search = $meli->api('/sites/#{siteId}/search',array(
		'q' => 'mp3',
	));
	    

[examples]: http://github.com/foocoders/meli-php/blob/master/examples/example_search.php
