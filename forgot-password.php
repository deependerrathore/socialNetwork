<?php  
	include('classes/DB.php');
	if (isset($_POST['resetpassword'])) {
		$email = $_POST['email'];
		$cstring = TRUE;
		$token = bin2hex(openssl_random_pseudo_bytes(64,$cstring));	
		$user_id = DB::query('SELECT id FROM users WHERE email = :email',array(':email'=>$email))[0]['id'];
		
		DB::query('INSERT into password_tokens VALUES(null, :token ,:user_id)',array(':token'=>sha1($token),':user_id'=>$user_id));
		echo 'Email sent!';
		echo '<br />';
		echo $token;
	}
	
?>
<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
	<input type="email" name="email" placeholder="Email..."><p />
	<input type="submit" name="resetpassword" value="Reset Password">
</form>