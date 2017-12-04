<?php 
	include('classes/DB.php');

	if (isset($_POST['createaccount'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];

		//Need to fix if user don't put username password and email
		if (!DB::query('SELECT username FROM users WHERE username = :username', array(':username'=>$username))) {
				
			if (strlen($username) >=3 && strlen($username) <=32) {
				if (strlen($password) >=6 && strlen($password) <=60) {
					if (preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
						if (filter_var($email,FILTER_VALIDATE_EMAIL)) {	
							if (!DB::query('SELECT email FROM users WHERE email = :email',array(':email'=>$email))) {
								DB::query('INSERT INTO users VALUES (null,:username , :password, :email)', array(':username'=>$username,':password'=>password_hash($password,PASSWORD_BCRYPT),':email'=>$email));
								echo 'success!';
							}else{
								echo 'Email in use';
							}		
							
						}else{
							echo 'Invalid email address!';
						}
						
					}else{
						echo 'Invalid character in username';
					}	
				}else{
					echo 'Invalid length of password! must be between 6 to 60';
				}
			}else{
				echo 'Invalid length of username! must be between 3 to 32';
			}
			
		}else{
			echo 'User already exist!';
		}

		
	}


 ?>

 <h1>Register</h1>
 <form action="create-account.php" method="post">
 	<input type="username" name="username" placeholder="Username..."><p />
 	<input type="password" name="password" placeholder="Password..."><p />
 	<input type="email" name="email" placeholder="someone@somesite.com"><p />
 	<input type="submit" name="createaccount" value="Create Account">
 </form>