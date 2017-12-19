<?php 
	include('classes/DB.php');
	include ('classes/Login.php');

	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
	}else{
		die('User not logged in');
	}

?>

<h1>Send a message</h1>
<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']) ?>" method="POST">
	<textarea rows="10" cols="80" name="body"></textarea>
	</br>
	<input type="submit" name="send" value="Send Message">
</form>