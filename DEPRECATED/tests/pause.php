<?php

require '../dBug.php';
require '../src/meli.php';

$meli = new Meli( array('appId' => '5804', 'secret' => '') );

$userId = $meli->initConnect();

if ($userId) {
	$pauseAction = array('status' => 'paused');
	if (isset($_REQUEST['item_id']) == 1)
		$response = $meli->putWithAccessToken('/items/' . $_REQUEST['item_id'], $pauseAction);

	$session_content = $meli->getAccessToken();
}
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title>Meli PHP SDK</title>
</head>
<body>
	<?php if ($userId): ?>
	<h1>Meli-PHP Pause Test - <strong style="color: #009900">[Logged in]</strong></h1>
	<div>
		<form method="POST">
			<fieldset>
				<label for="item_id">Item ID</label>
				<input type="text" name="item_id" id="item_id" size="20">
				<input type="submit" value="Enviar">
			</fieldset>
		</form>
	</div>
	<br />
	<a href="<?php echo $meli->getLogoutUrl(); ?>">Logout</a>
		<?php if($response) : ?>
		<hr/>
		<p>
			<h2>Response</h2>
		</p>
		<?php new dBug($response); endif; ?>
	<hr/>
	<p>
		<h2>Session Data</h2>
	</p>
	<?php
		new dBug($session_content);
		if(!preg_match('/items_write|item_management/', $session_content['scope'])) : ?>
			<h4 style="color: red">Attention: you have no items_write or item_management in your authorization scope.</h4>
		<?php else: ?>
			<h4 style="color: #009900">Cool! You have items_write or item_management in your authorization scope.</h4>
		<?php endif ?>
	<?php else: ?>
	<h1>Meli-PHP Pause Test - <strong style="color: red;">[Not logged in]</strong></h1>
	<div>
		<a href="<?php echo $meli->getLoginUrl( array('scope' => array('item_management', 'items_write', 'offline_access', 'read_basic')) ) ?>">Click here to Login</a>
	</div>
	<?php endif; ?>
</body>
</html>