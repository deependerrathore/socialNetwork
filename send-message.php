<?php
	session_start(); 
	include('classes/DB.php');
	include ('classes/Login.php');
	$cstring = TRUE;
	$token = bin2hex(openssl_random_pseudo_bytes(64,$cstring));
	if (isset(!$_SESSION['token'])) {
		$_SESSION['token'] = $token;	
	}
	
	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
	}else{
		die('User not logged in');
	}

	if (isset($_POST['send'])) {

		if (!isset($_POST['nocsrf'])) {
			die('Invalid token!');
		}

		if ($_POST['nocsrf'] != $_SESSION['token']) {
			die('Invalid token!');
		}
		
		if (strlen($_POST['body'] > '0' )) {
			if (DB::query('SELECT id FROM users WHERE id= :receiver',array(':receiver'=>$_GET['receiver']))) {

				DB::query('INSERT INTO messages VALUES (null,:body,:sender,:receiver,0)',array(':body'=>$_POST['body'],':sender'=>$userid, ':receiver'=>htmlspecialchars($_GET['receiver'])));

				echo 'Message Sent!';		
			}else{
				die('Invalid ID!');
			}
		}else{
			echo 'Please type in a message!';
		}
		
		session_destroy();
		
	}
?>

<h1>Send a message</h1>
<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']) ?>" method="POST">
	<textarea rows="10" cols="80" name="body"></textarea>
	<input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token']; ?>">
	</br>
	<input type="submit" name="send" value="Send Message">
</form>