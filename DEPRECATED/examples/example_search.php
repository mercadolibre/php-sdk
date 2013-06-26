<?php
require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> 'MeliPHPAppId',
	'secret' 	=> 'MeliPHPSecret',
));

if(isset($_REQUEST['q'])):
	
	$query = $_REQUEST['q'];
	
	$search = $meli->get('/sites/#{siteId}/search',array(
		'q' => $query)
	);
	
endif;
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
   	<title>MeliPHP SDK - Example Search</title>
</head>
<body>
	
	<h1>MeliPHP SDK - Example Search</h1>
    
    <form>
    	<input name="q" value="<?php echo $query; ?>" />
    	<input type="submit" name="search" value="search"/>
    </form>
	
	<ol>
	<?php
		foreach ($search['json']['results'] as &$searchItem):
		   echo '<li><a href="' . $searchItem['permalink'] . '">'. $searchItem['title'].'</a></li>';
		endforeach;
	?>
    </ol>

</body>
</html>