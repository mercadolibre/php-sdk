<?php

require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
    'appId'     => getenv('MeliPHPAppId'),
    'secret'    => getenv('MeliPHPSecret'),
));


if (isset($_REQUEST['item_id']) && $_REQUEST['item_id'] != null) {

    $itemId = $_REQUEST['item_id'];

    $item = $meli -> get('/items/' . $itemId);
    
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>MeliPHP SDK - Example Item</title>
	</head>
	<body>
		<h1>MeliPHP SDK - Example Item</h1>
		<form>
			<input name="item_id" value="<?php echo isset($item)?$itemId:'';?>" />
			<input type="submit" name="show item" value="show item"/>
		</form>
		
		<?php if (isset($item)):	?>
		<p><?php var_dump($item) ?></p>
		<?php endif?>
		
		
	</body>
</html>