<?php
// You can use this code in the examples folder
require_once('../vendor/autoload.php');
// Or you can put them in to the main folder
// require_once(__DIR__ . '/vendor/autoload.php');

// Remember to get the authorization code
// Make your call in the front of your application like this
// http://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=$APP_ID&redirect_uri=$YOUR_URL 

// Once the user is redirected to your callback url you'll
// receive in the query string, a parameter named code.
// You'll need this for the next part of the process.

$apiInstance = new Meli\Api\OAuth20Api(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$grant_type = 'authorization_code'; // Or 'refresh_token' if you need get one new token
$client_id = 'client_id_example'; // Your client_id
$client_secret = 'client_secret_example'; // Your client_secret
$redirect_uri = 'redirect_uri_example'; // Your redirect_uri
$code = 'code_example'; // The parameter CODE who was received in the query.
$refresh_token = ''; // Your refresh_token

try {
    $result = $apiInstance->getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling OAuth20Api->getToken: ', $e->getMessage(), PHP_EOL;
}
