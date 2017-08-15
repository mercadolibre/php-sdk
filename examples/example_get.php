<?php
require '../Meli/meli.php';

$appId = getenv('App_ID');
$secretKey = getenv('Secret_Key');
$redirectURI = getenv('Redirect_URI');

$meli = new Meli($appId, $secretKey);
$redirectURI = $redirectURI;

$params = array();

$result = $meli->get('/sites/MLB', $params);

echo '<pre>';
print_r($result);
echo '</pre>';