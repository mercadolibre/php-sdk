<?php
require '../src/meli.php';

// Create our Application instance (replace this with your appId and secret).
$meli = new Meli(array(
	'appId'  	=> 'MeliPHPAppId',
	'secret' 	=> 'MeliPHPSecret',
));

$userId = $meli -> initConnect();

// Login or logout url will be needed depending on current user state.
if ($userId):
	
	if(isset($_REQUEST['question_id']) == 1):

        $response = $meli -> postWithAccessToken('/answers', array('question_id' => $_REQUEST['question_id'], 'text' => $_REQUEST['answer_text']));

        $_SESSION['answer_question'] = true;

        header("Location: " . $meli -> getCurrentUrl(), TRUE, 302);

    endif;

    $user = $meli -> getWithAccessToken('/users/me');

    $unansweredQuestions = $meli -> getWithAccessToken('/questions/search', array('seller' => $user['json']['id'], 'status' => 'UNANSWERED'));

endif;
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title>MeliPHP SDK - Example questions</title>
</head>
<body>
	
	<h1>MeliPHP SDK - Example questions</h1>
	<?php if ($userId): ?>
		<p>Hello <?php echo $user['json']['first_name'] ?></p>
		
		<a href="<?php echo $meli -> getLogoutUrl();?>">Logout</a>
		
		<h2> Unanswered Questions </h2>
		<ul>
			<?php foreach ($unansweredQuestions['json']['questions'] as $question):	?>
			<li>
				<p><?php echo $question['text'] ?></p>
				<form method="POST">
					<input type="hidden" name="question_id" value="<?=$question['id']?>" />
					<textarea name="answer_text" cols="50" rows="3"></textarea>
					<input type="submit" value="Send" />
				</form>
			</li>
			<?php endforeach;?>
		</ul>
	<?php else:?>
		<div>
			<p>Login using OAuth 2.0 handled by the PHP SDK:</p>
			<a href="<?php echo $meli -> getLoginUrl(array('scope' => array('questions_write')));?>">Login with MercadoLibre</a>
		</div>
	<?php endif?>
</body>
</html>