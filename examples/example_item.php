<?php
require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> 'MeliPHPAppId',
	'secret' 	=> 'MeliPHPSecret',
));

if (isset($_REQUEST['item_id']) && $_REQUEST['item_id'] != null):
    $itemId = $_REQUEST['item_id'];
	$item = $meli -> get('/items/' . $itemId);
endif;
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
		<input name="item_id" value="<?php echo isset($item['json']) ? $itemId : ''; ?>" />
		<input type="submit" name="show item" value="show item"/>
	</form>
		
	<?php if (isset($item['json'])):	?>
		<p><?php var_dump($item['json']) ?></p>
	<?php endif ?>	

</body>
</html>