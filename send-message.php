<?php 
	include('classes/DB.php');
	include ('classes/Login.php');

	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
	}else{
		die('User not logged in');
	}

	if (isset($_POST['send'])) {
		
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
		
		
	}
?>

<h1>Send a message</h1>
<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']) ?>" method="POST">
	<textarea rows="10" cols="80" name="body"></textarea>
	</br>
	<input type="submit" name="send" value="Send Message">
</form>