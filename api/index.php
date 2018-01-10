<?php 

	require_once('DB.php');

	$db = new DB("127.0.0.1","SocialNetwork","root","admin");
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($_GET['url'] == 'auth') {
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);

			$username = $postBody->username;
			$password = $postBody->password;

			if ($db->query('SELECT username FROM users WHERE username = :username' , array(':username'=>$username))) {
				if(password_verify($password,$db->query('SELECT password FROM users WHERE username = :username',array(':username' => $username))[0]['password'])){
					$cstring = TRUE;
					$token = bin2hex(openssl_random_pseudo_bytes(64,$cstring));
				

					$user_id = $db->query('SELECT id FROM users WHERE username = :username',array(':username'=>$username))[0]['id'];
					$db->query('INSERT into login_tokens VALUES(null, :token ,:user_id)',array(':token'=>sha1($token),':user_id'=>$user_id));

					echo '{"token":"'.$token.'"}';
				}else{
					echo '{"Error":"Invalid username or password!"}';
					http_response_code(401);
				}
			}else{
				echo '{"Error":"Invalid username or password!"}';
				http_response_code(401);
			}
		}
	}else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		echo json_encode($db->query('SELECT * FROM users'));
		http_response_code(200);
	}else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
		if ($_GET['url']=='auth') {
			if ($db->query('SELECT token from login_tokens WHERE token=:token',array(':token'=>sha1($_GET['token'])))) {
				
			
				if (isset($_GET['token'])) {
					$db->query('DELETE FROM login_tokens WHERE token = :token' ,array(':token'=>sha1($_GET['token'])));
					echo '{"status":"success"}';
					http_response_code(200);
				}else{
					echo '{"Error":"Bad request"}';
					http_response_code(400);
				}

			}else{
				echo '{"Error":"Invalid token"}';
				http_response_code(400);
			}	
		}
		http_response_code(200);
	}else{
		http_response_code(405);
	}
 ?>