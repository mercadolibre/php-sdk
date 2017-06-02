<?php

session_start('test');

require 'vendor/autoload.php';

use \Meli\Meli;


$meli = new Meli('', '');

if($_GET['code']) {
	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], 'http://somecallbackurl');
	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] = $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refresh_token'] = $user['body']->refresh_token;
	// We can check if the access token in invalid checking the time
	if($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			echo '<pre>'.print_r($meli->refreshAccessToken(),true).'</pre>';
		} catch (Exception $e) {
			echo 'Exception: '. $e->getMessage() ;
		}
	}
	// We construct the item to POST
	$item = [
		"title" 		     => "Rayban Gloss Black",
		"category_id"		 => "MLV1227",
		"price" 		     => 10,
		"currency_id" 		 => "VEF",
		"available_quantity" => 1,
		"buying_mode" 		 => "buy_it_now",
		"listing_type_id"    => "bronze",
		"condition" 		 => "new",
		"description" 		 => "Item:, <strong> Ray-Ban WAYFARER Gloss Black RB2140 901 </strong> Model: RB2140. Size: 50mm. Name: WAYFARER. Color: Gloss Black. Includes Ray-Ban Carrying Case and Cleaning Cloth. New in Box",
		"video_id" 			 => "RXWn6kftTHY",
		"warranty"  	     => "12 month by Ray Ban",
		"pictures" 			=> [
			[
				"source" => "https://upload.wikimedia.org/wikipedia/commons/f/fd/Ray_Ban_Original_Wayfarer.jpg"
			],
			[
				"source" => "https://upload.wikimedia.org/wikipedia/commons/a/ab/Teashades.gif"
			]
		]
	];

	// We call the post request to list a item
	$result = $meli->post('/items', $item, ['access_token' => $_SESSION['access_token']]);

	echo '<pre>'.print_r($result,true).'</pre>';


} else {
	echo '<a href="' . $meli->getAuthUrl('http://somecallbackurl', Meli::$AUTH_URL['MLV']) . '">Login using MercadoLibre oAuth 2.0</a>';  //  Don't forget to set the autentication URL of your country.
}