<?php
session_start();

require '../Meli/meli.php';
require '../configApp.php';

$meli = new Meli($appId, $secretKey);

if($_GET['code']) {

	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], $redirectURI);

	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] = $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refresh_token'] = $user['body']->refresh_token;

	// We can check if the access token in invalid checking the time
	if($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			print_r($meli->refreshAccessToken());
		} catch (Exception $e) {
			echo "Exception: ",  $e->getMessage(), "\n";
		}
	}

	// We construct the item to POST
	$item = array(
		"title" => "Item De Teste - Por Favor, Não Ofertar! --kc:off",
        "category_id" => "MLB257111",
        "price" => 10,
        "currency_id" => "BRL",
        "available_quantity" => 1,
        "buying_mode" => "buy_it_now",
        "listing_type_id" => "bronze",
        "condition" => "new",
        "description" => "Item de Teste. Mercado Livre's PHP SDK.",
        "video_id" => "Q6dsRpVyyWs",
        "warranty" => "12 month",
        "pictures" => array(
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/IPhone_7_Plus_Jet_Black.svg/440px-IPhone_7_Plus_Jet_Black.svg.png"
            ),
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/IPhone7.jpg/440px-IPhone7.jpg"
            )
        ),
        "attributes" => array(
            array(
                "id" => "EAN",
                "value_name" => "190198043566"
            ),
            array(
                "id" => "COLOR",
                "value_id" => "52049"
            ),
            array(
                "id" => "WEIGHT",
                "value_name" => "188g"
            ),
            array(
                "id" => "SCREEN_SIZE",
                "value_name" => "4.7 polegadas"
            ),
            array(
                "id" => "TOUCH_SCREEN",
                "value_id" => "242085"
            ),
            array(
                "id" => "DIGITAL_CAMERA",
                "value_id" => "242085"
            ),
            array(
                "id" => "GPS",
                "value_id" => "242085"
            ),
            array(
                "id" => "MP3",
                "value_id" => "242085"
            ),
            array(
                "id" => "OPERATING_SYSTEM",
                "value_id" => "296859"
            ),
            array(
                "id" => "OPERATING_SYSTEM_VERSION",
                "value_id" => "iOS 10"
            ),
            array(
                "id" => "DISPLAY_RESOLUTION",
                "value_id" => "1920 x 1080"
            ),
            array(
                "id" => "BATTERY_CAPACITY",
                "value_name" => "3980 mAh"
            ),
            array(
                "id" => "FRONT_CAMERA_RESOLUTION",
                "value_name" => "7 mpx"
            )
        )
    );
	
	// We call the post request to list a item
	echo '<pre>';
	print_r($meli->post('/items', $item, array('access_token' => $_SESSION['access_token'])));
	echo '</pre>';

} else {

	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL['MLB']) . '">Login using MercadoLibre oAuth 2.0</a>';
}

