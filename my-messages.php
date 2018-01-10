<?php 
	include('classes/DB.php');
	include ('classes/Login.php');

	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
	}else{
		die('User not logged in');
	}

	if (isset($_GET['mid'])) {
		$message = DB::query('SELECT * FROM messages WHERE id = :mid AND receiver = :receiver OR sender = :sender',array(':mid'=>$_GET['mid'],':sender'=>$userid,':receiver'=>$userid))[0];
		echo '<h1>View Message</h1>';
		echo htmlspecialchars($message['body']);
		echo '<hr/>';

		if ($message['sender'] == $userid) {
			$id = $message['receiver']; 
		}else{
			$id = $message['sender'] ;
		}

		DB::query('UPDATE messages SET msgread = 1 WHERE id = :mid',array('mid'=>$_GET['mid']));
		?>
		<form action="send-message.php?receiver=<?php echo $id ?>" method="POST">
			<textarea rows="10" cols="80" name="body"></textarea>
			</br>
			<input type="submit" name="send" value="Send Message">
		</form>
		<?php
	}else{


?>

<h1>My Messages</h1>
<?php 
	 
	$messages = DB::query('SELECT messages.* , users.username FROM  messages INNER JOIN users ON users.id = messages.sender WHERE receiver=:receiver OR sender=:sender ',array(':receiver'=>$userid,':sender'=>$userid));

	foreach ($messages as $message) {

		if (strlen($message['body']) > 10) {
			$m = substr($message['body'] , 0,10) . "...";
		}else{
			$m = $message['body'] ;
		}
		if ($message['msgread'] ==0) {
			echo "<a href='my-messages.php?mid=".$message['id']."'><strong>". $m. "</strong></a> sent by <i>" .$message['username'].'</i><hr/>';	
		}else{
			echo "<a href='my-messages.php?mid=".$message['id']."'>".$m . "</a> sent by <i>" .$message['username'].'</i><hr/>';
		}
		
	}
	}
 ?>