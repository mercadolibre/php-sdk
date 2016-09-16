<?php
session_start();

require '../Meli/meli.php';

$meli = new Meli('APP_ID', 'SECRET_KEY');

if($_GET['code']) {

	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], 'http://localhost/PHPSDK/examples/example_login.php');

	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] = $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refrsh_token'] = $user['body']->refresh_token;

	// We can check if the access token in invalid checking the time
	if($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			print_r($meli->refreshAccessToken());
		} catch (Exception $e) {
			echo "Exception: ",  $e->getMessage(), "\n";
		}
	}

	// We construct the item to POST
	$item = array(
		"title" => "Rayban Gloss Black",
		"category_id" => "MLB1227",
		"price" => 10,
		"currency_id" => "BRL",
		"available_quantity" => 1,
		"buying_mode" => "buy_it_now",
		"listing_type_id" => "bronze",
		"condition" => "new",
		"description" => "Item:, <strong> Ray-Ban WAYFARER Gloss Black RB2140 901 </strong> Model: RB2140. Size: 50mm. Name: WAYFARER. Color: Gloss Black. Includes Ray-Ban Carrying Case and Cleaning Cloth. New in Box",
		"video_id" => "RXWn6kftTHY",
		"warranty" => "12 month by Ray Ban",
		"pictures" => array(
			array(
				"source" => "https://upload.wikimedia.org/wikipedia/commons/f/fd/Ray_Ban_Original_Wayfarer.jpg"
			),
			array(
				"source" => "https://upload.wikimedia.org/wikipedia/commons/a/ab/Teashades.gif"
			)
		)
	);
	
	// We call the post request to list a item
	echo '<pre>';
	print_r($meli->post('/items', $item, array('access_token' => $_SESSION['access_token'])));
	echo '</pre>';
} else {

	echo '<a href="' . $meli->getAuthUrl('http://localhost/PHPSDK/examples/example_login.php', Meli::$AUTH_URL['MLB']) . '">Login using MercadoLibre oAuth 2.0</a>';
}

