<?php
session_start();

require '../Meli/meli.php';
require '../configApp.php';

$meli = new Meli($appId, $secretKey);

if(isset($_GET['code']) || isset($_SESSION['access_token'])) {

	// If code exist and session is empty
	if(isset($_GET['code']) && !isset($_SESSION['access_token'])) {
		// //If the code was in get parameter we authorize
		try{
			$user = $meli->authorize($_GET["code"], $redirectURI);
			
			// Now we create the sessions with the authenticated user
			$_SESSION['access_token'] = $user['body']->access_token;
			$_SESSION['expires_in'] = time() + $user['body']->expires_in;
			$_SESSION['refresh_token'] = $user['body']->refresh_token;
		}catch(Exception $e){
			echo "Exception: ",  $e->getMessage(), "\n";
		}
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
	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]) . '">Login using MercadoLibre oAuth 2.0</a>';
}

