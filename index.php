<?php 
	include('classes/DB.php');
	include ('classes/Login.php');
	include 'classes/Post.php';
	include 'classes/Comment.php';

	$showTimeline = FALSE;
	if (Login::isLoggedIn()) {
		$userid = Login::isLoggedIn();
		$showTimeline = TRUE;
	}else{
		echo 'User not logged in';
	}
	if (isset($_GET['postid'])) {
		Post::likePost($_GET['postid'],$userid);
				
	}

	if (isset($_POST['comment'])) {
		
		Comment::createComment($_POST['commentbody'],$_GET['postid'],$userid);
				
	}
	$followingposts = DB::query('SELECT posts.id, posts.post,posts.likes,users.username FROM posts,followers,users
	WHERE posts.user_id = followers.user_id
	and posts.user_id = users.id
	AND followers.follower_id = :userid
	ORDER BY posts.likes DESC',array(':userid'=>Login::isLoggedIn()));

	foreach ($followingposts as $post) {
		
		echo $post['post'] . "~". $post['username'] ;
		echo "<form action='index.php?postid=".$post['id'] . "' method='POST'>";
 		if(!DB::query('SELECT post_id FROM post_likes WHERE post_id = :postid AND user_id = :userid', array(':postid'=>$post['id'] ,':userid'=>$userid))){
 			echo "<input type='submit' name='like' value='Like'>";
 		}else{
 			echo "<input type='submit' name='unlike' value='Unlike'>";
 		}
 		echo "<span>". $post['likes']. " likes </span>
 		 
 		</form>
 		<form action='index.php?postid=".$post['id']."' method='POST'>
 			<textarea rows='4' cols='55' name='commentbody'></textarea>
 			<input type='submit' name='comment' value='Comment'>
 		</form>
 		<hr> <br />";
 		
		
	}
 ?>
