<?php
require 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$access_token = getenv('TEST_ACCESS_TOKEN', null);
$refresh_token = getenv('TEST_REFRESH_TOKEN', null);
$client_id = getenv('MELI_CLIENT_ID', null);
$client_secret = getenv('MELI_CLIENT_SECRET', null);

echo '<pre>';

$meli = new Meli\Meli('MLB', [
        'client_id' => $client_id, 
        'client_secret' => $client_secret, 
        'access_token' => $access_token, 
        'refresh_token' => $refresh_token
    ]);
$user = new Meli\MeliUser($meli);
print_r($user->getUser(2207803321));