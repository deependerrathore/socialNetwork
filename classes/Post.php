<?php 
/**
 * 
 */
 class Post
 {
 	public static function createPost($postbody,$loggedInUser,$profileUserId){
 		
		
		if (strlen($postbody) >255 || strlen($postbody) < 1) {
			die('Incorrect lenght!');
		}
		if ($loggedInUser == $profileUserId) {
			DB::query('INSERT INTO posts VALUES (null,:post,now(),:userid,0)',array(':post'=>$postbody,':userid'=>$profileUserId));
		}else{
			die('You are not allowed to post on others profile!');
		}
 	}

 	public static function likePost($postId , $likerId){
 		if (!DB::query('SELECT user_id FROM post_likes WHERE user_id = :userid AND post_id =:postid',array(':userid'=>$likerId , ':postid'=>$postId))) {
			DB::query('UPDATE posts SET likes = likes + 1 WHERE id = :postid',array(':postid'=>$postId));
			DB::query('INSERT INTO post_likes VALUES (null,:postid,:userid)',array(':postid'=>$postId,':userid'=>$likerId));
		}else{
			//echo 'already liked!';
			DB::query('UPDATE posts SET likes = likes  - 1 WHERE id = :postid',array(':postid'=>$postId));
			DB::query('DELETE FROM post_likes WHERE post_id = :postid AND user_id =:userid',array(':postid'=>$postId,':userid'=>$likerId));
		}
 	}

 	public static function displayPosts($userid,$username,$loggedInUserId){
 		$dbposts = DB::query('SELECT * FROM posts WHERE user_id =:userid ORDER BY id DESC',array(':userid'=>$userid));
		$posts  = "";
			foreach ($dbposts as $p) {

				if(!DB::query('SELECT post_id FROM post_likes WHERE post_id = :postid AND user_id = :userid', array(':postid'=>$p['id'] ,':userid'=>$loggedInUserId))){
					$posts .= htmlspecialchars($p['post']) . "
				 	<form action='profile.php?username=$username&postid=".$p['id'] . "' method='POST'>
 						<input type='submit' name='like' value='Like'>
 						<span>". $p['likes']. "</span>
 					</form>
					<hr> <br />
					";	
				}else{
					$posts .= htmlspecialchars($p['post']) . "
				 	<form action='profile.php?username=$username&postid=".$p['id'] . "' method='POST'>
 						<input type='submit' name='unlike' value='Unlike'>
 						<span>". $p['likes']. "</span>
 					</form>
					<hr> <br />
					";	
				}
				//print_r($p['post']);
				
			}
		return $posts;	
 	}
 } 