<?php


require '../src/meli.php';

// Create our Application instance (replace this with your country, appId and secret).
$meli = new Meli(array(
	'countryId' => 'ar',
	'appId'  	=> '4459',
	'secret' 	=> 'kKoqUtvm9NXx5EnhmPM4xzgM08HFzrBU',
));

$userId = $meli->getUserId();

// Login or logout url will be needed depending on current user state.
if ($userId) {
  $logoutUrl = $meli->getLogoutUrl();
} else {
  $loginUrl = $meli->getLoginUrl();
}

?>
<!doctype html>
<html>
  <head>
	<meta charset="UTF-8"/>
    <title>MeliPHP SDK - Login</title>
  </head>
  <body>
    <h1>Login</h1>


    <?php if ($userId): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with MercadoLibre</a>
      </div>
    <?php endif ?>
    
  </body>
</html>