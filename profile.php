<?php 
	include('classes/DB.php');
	include ('classes/Login.php');
	include ('classes/Post.php');
	include ('classes/Image.php');
	$username = "";
	$verified = FALSE;
	$isFollowing = FALSE;

	if(isset($_GET['username'])){
		if (DB::query('SELECT username from users WHERE username = :username',array(':username'=>$_GET['username']))) {
			$username = DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['username'];
			$userid = DB::query('SELECT id FROM users WHERE username = :username',array(':username'=>$_GET['username']))[0]['id'];
			$verified = DB::query('SELECT verified FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['verified'];
			$followerid = Login::isLoggedIn();

			
			
			if (isset($_POST['follow'])) {
				if ($userid != $followerid) {
					if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id=:followerid',array(':userid'=>$userid,':followerid'=>$followerid))) {

						if ($followerid == 19) { //userid of the account we have created for the verfication
							DB::query('UPDATE users SET verified = 1 WHERE id = :userid',array(':userid'=>$userid));
						}
						DB::query('INSERT INTO followers values (null,:userid,:followerid)',array(':userid'=>$userid,':followerid'=>$followerid));
					}else{
						echo 'Already following!';
					}
					$isFollowing = TRUE;
				}
				
			}

			if (isset($_POST['unfollow'])) {
				if ($userid != $followerid) {
					if (DB::query('SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id =:followerid',array(':userid'=>$userid,':followerid'=>$followerid))) {
						if ($followerid == 19) {
							DB::query('UPDATE users SET verified = 0 WHERE id = :userid',array(':userid'=>$userid));
						}
						DB::query('DELETE FROM followers WHERE user_id =:userid AND follower_id =:followerid',array(':userid'=>$userid,':followerid'=>$followerid));
					}
					$isFollowing = FALSE;
				}
			}

			if (isset($_POST['deletepost'])) {
				//echo 'Clicked on delete';				
				if(DB::query('SELECT id FROM posts WHERE id = :postid AND user_id=:userid',array(':postid'=>$_GET['postid'] , ':userid'=>$followerid))){
					DB::query('DELETE FROM posts WHERE id = :postid AND user_id = :userid',array(':postid'=>$_GET['postid'] , ':userid'=>$followerid));
					//DB::query('DELETE FROM post_likes WHERE post_id=:postid',array(':postid' => $_GET['postid']));
					echo 'post deleted!';
				}
			}

			if (DB::query('SELECT follower_id FROM followers WHERE user_id = :userid AND follower_id =:followerid',array(':userid'=>$userid,':followerid'=>$followerid))) {
				//echo 'Already following!';
				$isFollowing = TRUE;

			}


			if (isset($_POST['post'])) {
				if ($_FILES['postimg']['size'] == 0 ) {
				
					Post::createPost($_POST['postbody'], Login::isLoggedIn(),$userid);
				
				}else{
					$postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(),$userid);
					Image::uploadImage('postimg','UPDATE posts SET postimg = :postimg WHERE id = :postid',array(':postid'=>$postid));
				}
				
			}	

			if (isset($_GET['postid']) && !isset($_POST['deletepost'])) {
				Post::likePost($_GET['postid'],$followerid);
				
			}

			$posts = Post::displayPosts($userid,$username,$followerid);
			
		}else{
			die('User not found!');
		}
	}
 ?>
 <h1><?php echo $username;?>'s Profile<?php if($verified){echo ' - Verified';} ?></h1>

 <form action="profile.php?username=<?php echo $username;?>" method="post">
 	<?php 
 	if ($userid != $followerid) {
 		if ($isFollowing) {
 			echo '<input type="submit" name="unfollow" value="UnFollow">';
 		}else{
 			echo '<input type="submit" name="follow" value="Follow">';
 		}	
 	}
 	
 	?>
 </form>

 
 <form action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
 	<textarea rows="10" cols="80" name="postbody"></textarea>
	</br>
	Upload an image:
	<input type="file" name="postimg">
	<input type="submit" name="post" value="Post">
</form>


<div class="posts">
	<?php echo $posts; ?>
</div>