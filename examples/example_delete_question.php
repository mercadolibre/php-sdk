<?php

session_start('test');

require 'vendor/autoload.php';

use \Meli\Meli;


$meli = new Meli('', '');

if ($_GET['code']) {
	
	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], 'http://somecallbackurl');
	
	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] 	= $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refrsh_token'] 	= $user['body']->refresh_token;
	// We can check if the access token in invalid checking the time
	if ($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			echo '<pre>' . print_r($meli->refreshAccessToken(), true) . '</pre>';
		} catch (Exception $e) {
		  	echo 'Exception: ' . $e->getMessage();
		}
	}
	
	$meli->delete('/questions/QUESTION_ID', ['access_token' => $_SESSION['access_token']]);
	
} else {
	echo '<a href="' . $meli->getAuthUrl('http://somecallbackurl', Meli::$AUTH_URL['MLV']) . '">Login using MercadoLibre oAuth 2.0</a>'; //  Don't forget to set the autentication URL of your country.
}