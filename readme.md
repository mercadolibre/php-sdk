MeliPHP unofficial MercadoLibre PHP SDK (v.0.0.1)
==========================

This repository contains the open source MeliPHP Unofficial PHP SDK that allows you to access MercadoLibre Platform from your PHP app. 
MeliPHP is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

		require '../src/meli.php';
	
		// Create our Application instance (replace this with your country, appId and secret).
		$meli = new Meli(array(
			'countryId' => 'ar',
			'appId'  	=> getenv('MeliPHPAppId'),
			'secret' 	=> getenv('MeliPHPSecret'),
		));

You can create your app in [http://en.mercadolibre.io/aplicaciones](http://en.mercadolibre.io/aplicaciones)


Examples
--------

* Login with MercadoLibre
	
		
		$userId = $meli->getUserId();
		
		// Login or logout url will be needed depending on current user state.
		if ($userId) {
		  $user = $meli->get(true,'/users/me');
		}

	[full code](http://github.com/foocoders/meli-php/blob/master/examples/example_login.php) ,
	[running](http://meliphp.phpfogapp.com/examples/example_login.php)

* Search items
 	
		$query = $_REQUEST['q'];
	
		$search = $meli->get(false,'/sites/#{siteId}/search',array(
		'q' => $query,
		));
	
	[full code](http://github.com/foocoders/meli-php/blob/master/examples/example_search.php)
	[running](http://meliphp.phpfogapp.com/examples/example_search.php)

* View item

	[full code](http://github.com/foocoders/meli-php/blob/master/examples/example_item.php)
	[running](http://meliphp.phpfogapp.com/examples/example_item.php)
