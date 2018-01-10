<?php 
	include('classes/DB.php');
	include ('classes/Login.php');


	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
	}else{
		die('User not logged in');
	}
	echo "<h1>Notifications</h1>";
	if (DB::query('SELECT * FROM notifications WHERE receiver =:userid',array(':userid'=>$userid))) {
		$notifications = DB::query('SELECT * FROM notifications WHERE receiver =:userid ORDER BY id DESC',array(':userid'=>$userid));

		foreach ($notifications as $n) {
			if ($n['type'] == 1) {
				 $senderName = DB::query('SELECT username FROM users WHERE id = :senderid',array(':senderid'=>$n['sender']))[0]['username'];

				 $extra = json_decode($n['extra']);

				 if ($extra == "") {
				 	echo 'You got a new notification';

				 }else{
				 	echo $senderName . " mentioned you in a post! - " .$extra->postbody ." <hr/>";	
				 }
				 
			}else if ($n['type'] == 2) {
				 $senderName = DB::query('SELECT username FROM users WHERE id = :senderid',array(':senderid'=>$n['sender']))[0]['username'];

				 echo $senderName . " likes your post! <hr/>";	
				 
			}
		}
	}

?>