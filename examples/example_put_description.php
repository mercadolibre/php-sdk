<?php
session_start();

require '../Meli/meli.php';
require '../configApp.php';

$meli = new Meli($appId, $secretKey);

if($_GET['code']) {
	
	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], $redirectURI);
	
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
	
	$params = array('access_token' => $_SESSION['access_token']);

	$body = array('text' => 'Adding new description <strong>html</strong>');

	$response = $meli->put('/items/MLB12343412/descriptions', $body, $params);
	
} else {
	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL['MLB']) . '">Login using MercadoLibre oAuth 2.0</a>';
}