<?php
session_start();
require 'Meli/meli.php';
require 'configApp.php';



echo '<a href="examples/example_login.php"><b>Login using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_get.php"><b>GET using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_list_item.php"><b>List Item using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_put_description.php"><b>PUT using MercadoLibre oAuth 2.0</b></a><br>';
echo '<a href="examples/example_delete_question.php"><b>Delete question using MercadoLibre oAuth 2.0</b></a><br>';

echo "<br>App_ID<br>";
echo $appId;

echo "<br>Secret_Key<br>";
echo $secretKey

echo "<br>Redirect_URI<br>";
echo $redirectURI;

echo "<br>Site App<br>";
echo $countryApp;



$meli = new Meli($appId, $secretKey);
$redirectURI = $redirectURI;

if($_GET['code'] || $_SESSION['access_token']) {

	// If code exist and session is empty
	if($_GET['code'] && !($_SESSION['access_token'])) {
		// If the code was in get parameter we authorize
		$user = $meli->authorize($_GET['code'], $redirectURI);
		
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
	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL[$countryApp]) . '">Login using MercadoLibre oAuth 2.0</a>';
}
