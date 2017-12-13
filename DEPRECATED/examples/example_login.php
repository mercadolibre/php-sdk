<?php
require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> 'MeliPHPAppId',
	'secret' 	=> 'MeliPHPSecret',
));

$userId = $meli->initConnect();

// Login or logout url will be needed depending on current user state.
if ($userId):
	$user = $meli->getWithAccessToken('/users/me');
endif;

?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
    <title>MeliPHP SDK - Example login</title>
</head>
<body>

	<h1>MeliPHP SDK - Example login</h1>
    
    <?php if ($userId): ?>
		<p>Hello <?php echo $user['json']['first_name']  ?> </p>
		<a href="<?php echo $meli->getLogoutUrl(); ?>">Logout</a>
    <?php else: ?>
    	<div>
    		<p> Login using OAuth 2.0 handled by the PHP SDK: </p>
        	<a href="<?php echo $meli->getLoginUrl(); ?>">Login with MercadoLibre</a>
    	</div>
    <?php endif ?>
    
</body>
</html>