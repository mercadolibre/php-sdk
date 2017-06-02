<?php

require 'vendor/autoload.php';

use \Meli\Meli;

$meli = new Meli('', '');

$params = [];
$result = $meli->get('/sites/MLV', $params); //  Don't forget to set the autentication URL of your country.

// If you wish , you can get an associative array with param $assoc = true
// $result = $meli->get('/sites/MLV', $params, true);

echo '<pre>'.print_r($result,true).'</pre>';