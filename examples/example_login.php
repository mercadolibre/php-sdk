<?php
session_start('teste');

require '../MercadoLivre/meli.php';

$meli = new Meli('APP_ID', 'SECRET_KEY', $_SESSION['access_token'], $_SESSION['refresh_token']);

if($_GET['code'] || $_SESSION['access_token']) {

	// If code exist and session is empty
	if($_GET['code'] && !($_SESSION['access_token'])) {
		// If the code was in get parameter we authorize
		$user = $meli->authorize($_GET['code'], 'http://localhost/PHPSDK/examples/example_login.php');
		
		// Now we create the sessions with the authenticated user
		$_SESSION['access_token'] = $user['body']->access_token;
		$_SESSION['expires_in'] = time() + $user['body']->expires_in;
		$_SESSION['refresh_token'] = $user['body']->refresh_token;
	} else {
		// We can check if the access token in invalid checking the time
		if($_SESSION['expires_in'] < time()) {
			try {
				// Make the refresh proccess
				$refresh = $meli->refreshAccessToken();

				// Now we create the sessions with the new parameters
				$_SESSION['access_token'] = $refresh['body']->access_token;
				$_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
				$_SESSION['refresh_token'] = $refresh['body']->refresh_token;
			} catch (Exception $e) {
			  	echo "Exception: ",  $e->getMessage(), "\n";
			}
		}
	}

	echo '<pre>';
		print_r($_SESSION);
	echo '</pre>';
	
} else {
	echo '<a href="' . $meli->getAuthUrl('http://localhost/PHPSDK/examples/example_login.php') . '">Login using MercadoLibre oAuth 2.0</a>';
}
