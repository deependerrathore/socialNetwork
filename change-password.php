<?php 
	include('classes/DB.php');
	include ('classes/Login.php');
	$tokenIsValid = FALSE;	
	if (Login::isLoggedIn()) {
		if (isset($_POST['changepassword'])) {
			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$newpasswordrepeat = $_POST['newpasswordrepeat'];

			$userid = Login::isLoggedIn();
			if(password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id = :userid' ,array(":userid"=>$userid))[0]['password'])){
				if ($newpassword == $newpasswordrepeat) {
					if (strlen($newpassword) >=6 && strlen($newpassword) <=60) {


						DB::query('UPDATE users SET password = :newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword,PASSWORD_BCRYPT),':userid'=>$userid));
						echo 'Password changed successfully!';
					}
				}else{
					echo 'password not matching';
				}
			}else{
				echo 'Invalid old password!';
			}
		}
	}else{

		if (isset($_GET['token'])) {
			
			
			
			$token = $_GET['token'];

			if (DB::query('SELECT user_id FROM password_tokens where token =:token',array(':token'=>sha1($token)))) {
			
				$userid = DB::query('SELECT user_id FROM password_tokens where token =:token',array(':token'=>sha1($token)))[0]['user_id'];

				$tokenIsValid = TRUE;
				if (isset($_POST['changepassword'])) {
				
				$newpassword = $_POST['newpassword'];
				$newpasswordrepeat = $_POST['newpasswordrepeat'];

				
				
					if ($newpassword == $newpasswordrepeat) {
						if (strlen($newpassword) >=6 && strlen($newpassword) <=60) {


							DB::query('UPDATE users SET password = :newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword,PASSWORD_BCRYPT),':userid'=>$userid));
							echo 'Password changed successfully!';
							DB::query('DELETE FROM password_tokens WHERE user_id= :userid',array(':userid'=>$userid)); 
						}
					}else{
						echo 'password not matching';
					}
				
			}

			}else{
				die('Invalid token!');
			}
		}else{
			die('User not logged in');	
		}		
		
	}

 ?>

 <h1>Change your password</h1>
 <form action="<?php if(!$tokenIsValid){echo 'change-password.php';}else{ echo 'change-password.php?token='.$token.'';} ?>" method="post">
 	<?php if(!$tokenIsValid){ echo '<input type="password" name="oldpassword" placeholder="Current password..."><p />';}  ?>
 	<input type="password" name="newpassword" placeholder="New password..."><p />
 	<input type="password" name="newpasswordrepeat" placeholder="Repeat new password..."><p />
 	<input type="submit" name="changepassword" value="Change Password" >
 </form>