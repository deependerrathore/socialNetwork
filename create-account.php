<?php 
	include('classes/DB.php');

	if (isset($_POST['createaccount'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];


		DB::query('INSERT INTO users VALUES (null,:username , :password, :email)', array('username'=>$username,'password'=>$password,'email'=>$email));
		echo 'success!';
	}


 ?>

 <h1>Register</h1>
 <form action="create-account.php" method="post">
 	<input type="username" name="username" placeholder="Username..."><p />
 	<input type="password" name="password" placeholder="Password..."><p />
 	<input type="email" name="email" placeholder="someone@somesite.com"><p />
 	<input type="submit" name="createaccount" value="Create Account">
 </form>