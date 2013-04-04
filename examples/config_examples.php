<?php
// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> getenv('MeliPHPAppId'),
	'secret' 	=> getenv('MeliPHPSecret'),
));

$userId = $meli -> initConnect();
?>
