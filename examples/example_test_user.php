<?php
require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> 'MeliPHPAppId',
 	'secret' 	=> 'MeliPHPSecret',
));

$userId = $meli->initConnect();
$siteId = null;
$testUser = null;

// Login or logout url will be needed depending on current user state.
if ($userId){
    if (isset($_REQUEST['site_id']) && $_REQUEST['site_id'] != null){
        $siteId = $_REQUEST['site_id'];
        
        $params = array("site_id" => $siteId);
        $testUser = $meli->postWithAccessToken('/users/test_user', $params);
    }
}

?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
    <title>MeliPHP SDK - Example Create a Test User</title>
</head>
<body>

	<h1>MeliPHP SDK - Example Create a Test User</h1>
    
    <?php if ($testUser): ?>
		<p>Here is the Test User data created for you: <?php print_r($testUser['json']); ?> </p>
		
	<?php else: ?>
	<div>
	    <p> Create a Test User for this site id: </p>
    	<form>
    		<input name="site_id" value="<?php echo isset( $siteId ) ? $siteId : 'MLA'; ?>" />
    		<input type="submit" name="create user" value="create user"/>
    	</form>
    </div>
    <?php endif ?>
    
</body>
</html>