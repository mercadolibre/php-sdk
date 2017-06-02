<?php

require 'vendor/autoload.php';

use \Meli\Meli;


$meli = new Meli('', '');

$access_token = '';

$params = [
    'access_token' => $access_token
];

$result = $meli->get('/users/me', $params);

echo '<pre>'.print_r($result,true).'</pre>';