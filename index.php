<?php
require 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use Meli\{Meli, User, MeliException};

$access_token = getenv('TEST_ACCESS_TOKEN', null);
$refresh_token = getenv('TEST_REFRESH_TOKEN', null);
$client_id = getenv('MELI_CLIENT_ID', null);
$client_secret = getenv('MELI_CLIENT_SECRET', null);

echo '<pre>';

$meli = new Meli('MLB', [
        'client_id' => $client_id, 
        'client_secret' => $client_secret, 
        'access_token' => $access_token, 
        'refresh_token' => $refresh_token
    ]);
$u = new User($meli);

try {
	print_r($u->search('HEMA1823884'));
} catch (MeliException $e) {
	var_dump($e->getData());
} catch (Exception $e) {
	var_dump($e->getMessage(), $e->getFile(), $e->getLine());
}