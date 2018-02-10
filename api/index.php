<?php 

	require_once('DB.php');

	$db = new DB("127.0.0.1","SocialNetwork","root","admin");
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		if ($_GET['url'] == 'users') {	
			$postBody = file_get_contents("php://input");
			$postBody = json_decode($postBody);
			$username = $postBody->username;
			$password = $postBody->password;
			$email = $postBody->email;
			if (!$db->query('SELECT username FROM users WHERE username = :username', array(':username'=>$username))) {
				
			if (strlen($username) >=3 && strlen($username) <=32) {
				if (strlen($password) >=6 && strlen($password) <=60) {
					if (preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
						if (filter_var($email,FILTER_VALIDATE_EMAIL)) {	
							if (!$db->query('SELECT email FROM users WHERE email = :email',array(':email'=>$email))) {
								$db->query('INSERT INTO users VALUES (null,:username , :password, :email,0,null)', array(':username'=>$username,':password'=>password_hash($password,PASSWORD_BCRYPT),':email'=>$email));
								echo '{"Success":"User created!"}';
								http_response_code(200);
							}else{
								echo '{"Error":"Email in use!"}';
								http_response_code(409);
							}		
							
						}else{
							echo '{"Error":"Invalid email address!"}';
							http_response_code(409);
							
						}
						
					}else{
						echo '{"Error":"Invalid character in username!"}';
						http_response_code(409);
					}	
				}else{
					echo '{"Error":"Invalid length of password! must be between 6 to 60!"}';
					http_response_code(409);
									}
			}else{
				echo '{"Error":"Invalid length of username! must be between 3 to 32!"}';
				http_response_code(409);
			}
			
		}else{
			echo '{"Error":"User already exist!"}';
			http_response_code(409);
		}

		}
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
		if ($_GET['url'] == 'likes') {

			$token = $_COOKIE['SNID'];

			$likerId = $db->query('SELECT user_id FROM login_tokens WHERE token = :token',array(':token'=>sha1($token)))[0]['user_id'];

			$postId = $_GET['id'] ;
			if (!$db->query('SELECT user_id FROM post_likes WHERE user_id = :userid AND post_id =:postid',array(':userid'=>$likerId , ':postid'=>$postId))) {
				$db->query('UPDATE posts SET likes = likes + 1 WHERE id = :postid',array(':postid'=>$postId));
				$db->query('INSERT INTO post_likes VALUES (null,:postid,:userid)',array(':postid'=>$postId,':userid'=>$likerId));

				//Notify::createNotify(null,$postId);
			}else{
				//echo 'already liked!';
				$db->query('UPDATE posts SET likes = likes  - 1 WHERE id = :postid',array(':postid'=>$postId));
				$db->query('DELETE FROM post_likes WHERE post_id = :postid AND user_id =:userid',array(':postid'=>$postId,':userid'=>$likerId));
			}
		}
	}else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		//echo json_encode($db->query('SELECT * FROM users'));
		if ($_GET['url'] == 'auth') {
			
		}else if ($_GET['url'] == 'users') {
				
		}else if ($_GET['url'] == 'posts') {

			$token = $_COOKIE['SNID'];

			$userid = $db->query('SELECT user_id FROM login_tokens WHERE token = :token',array(':token'=>sha1($token)))[0]['user_id'];


			$followingposts = $db->query('SELECT posts.id, posts.post,posts.likes,posts.posted_at,users.username FROM posts,followers,users
			WHERE posts.user_id = followers.user_id
			and posts.user_id = users.id
			AND followers.follower_id = :userid
			ORDER BY posts.likes DESC',array(':userid'=>$userid));

			$response = "[";
			foreach ($followingposts as $post) {
				
				$response.= "{";
				$response.= '"PostId":"'.$post['id'].'",';
				$response.= '"PostBody":"'.$post['post'].'",';
				$response.= '"PostedBy":"'.$post['username'].'",';
				$response.= '"PostedAt":"'.$post['posted_at'].'",';
				$response.= '"Likes":"'.$post['likes'].'"';
				$response.= "},";
		 		
		
			}

			$response = substr($response, 0,strlen($response)-1);
			$response.= "]";

			echo $response;
		}

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