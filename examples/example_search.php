<?php


require '../src/meli.php';

// Create our Application instance (replace this with your country, appId and secret).
$meli = new Meli(array(
	'countryId' => 'ar',
	'appId'  	=> '344617158898614',
	'secret' 	=> '6dc8ac871858b34798bc2488200e503d',
));

$search = $meli->get(false,'/sites/#{siteId}/search',array(
	'q' => 'mp3',
));

?>
<!doctype html>
<html>
  <head>
	<meta charset="UTF-8"/>
    <title>meli-php-sdk</title>
  </head>
  <body>
    <h1>search mp3</h1>


	<?php

		foreach ($search['results'] as &$searchItem) {
		   echo $searchItem['title']."<br>";
		}
	?>
    
  </body>
</html>