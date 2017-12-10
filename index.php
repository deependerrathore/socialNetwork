<?php 
	include('classes/DB.php');
	include ('classes/Login.php');

	$showTimeline = FALSE;
	if (Login::isLoggedIn()) {
		echo Login::isLoggedIn();
		$showTimeline = TRUE;
	}else{
		echo 'User not logged in';
	}

	$followingposts = DB::query('SELECT posts.post,posts.likes,users.username FROM posts,followers,users
	WHERE posts.user_id = followers.user_id
	and posts.user_id = users.id
	AND followers.follower_id = :userid
	ORDER BY posts.likes DESC',array(':userid'=>Login::isLoggedIn()));

	foreach ($followingposts as $post) {
		echo $post['post'] . "~". $post['username'] ." " .$post['likes']." likes <hr />";
	}
 ?>
