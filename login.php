<?php  
	include('classes/DB.php');
	if (isset($_POST['login'])) {
		
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(DB::query('SELECT username FROM users WHERE username = :username' , array(':username'=>$username))){
			
			if(password_verify($password,DB::query('SELECT password FROM users WHERE username = :username',array(':username' => $username))[0]['password'])){
				echo 'User logged in';
				$cstring = TRUE;
				$token = bin2hex(openssl_random_pseudo_bytes(64,$cstring));
				
				setcookie("SNID",$token,time() + 60 * 60 * 24 * 7 , '/',NULL,NULL, TRUE);
				$user_id = DB::query('SELECT id FROM users WHERE username = :username',array(':username'=>$username))[0]['id'];
				DB::query('INSERT into login_tokens VALUES(null, :token ,:user_id)',array(':token'=>sha1($token),':user_id'=>$user_id));
			}else{
				echo 'Incorrect password!';
			}
		}else{
			echo 'User not registered!';
		}
	}
?>
<h1>Login</h1>
<form action="login.php" method="post">
	<input type="username" name="username" placeholder="Username..."><p />
	<input type="password" name="password" placeholder="Password..."><p />
	<input type="submit" name="login" value="Login">
</form>