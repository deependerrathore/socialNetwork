<?php  
	include('classes/DB.php');
	if (isset($_POST['login'])) {
		
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(DB::query('SELECT username FROM users WHERE username = :username' , array(':username'=>$username))){
			echo $password;
			echo DB::query('SELECT password FROM users WHERE username = :username',array(':username' => $username))[0]['password'];
			if(password_verify($password,DB::query('SELECT password FROM users WHERE username = :username',array(':username' => $username))[0]['password'])){
				echo 'User logged in';
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