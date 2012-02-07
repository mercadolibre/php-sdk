<?php


require '../src/meli.php';

// Create our Application instance (replace this with your country, appId and secret).
$meli = new Meli(array(
	'countryId' => 'ar',
	'appId'  	=> '14459',
	'secret' 	=> 'kKoqUtvm9NXx5EnhmPM4xzgM08HFzrBU',
));

$userId = $meli->getUserId();

// Login or logout url will be needed depending on current user state.
if ($userId) {
  $user = $meli->get(true,'/users/me');
}

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
    <p>Hello <?php echo $user['first_name']  ?> </p>
      <a href="<?php echo $meli->getLogoutUrl(); ?>">Logout</a>
    <?php else: ?>
      <div>
       <p> Login using OAuth 2.0 handled by the PHP SDK: </p>
        <a href="<?php echo $meli->getLoginUrl(); ?>">Login with MercadoLibre</a>
      </div>
    <?php endif ?>
    
  </body>
</html>