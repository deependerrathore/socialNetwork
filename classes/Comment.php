<?php 

class Comment{
	public static function createComment($commentBody,$postId,$userid){
 		
		if (strlen($commentBody) >255 || strlen($commentBody) < 1) {
			
			die('Incorrect lenght!');
		}
		if (!DB::query('SELECT id FROM posts WHERE id = :postid',array(':postid'=>$postId))) {
			
			echo 'Invalid post ID';
		}else{
			
			DB::query('INSERT INTO comments VALUES (null,:comment,:userid,now(), :postid)',array(':comment'=>$commentBody,':userid'=>$userid,':postid'=>$postId));
		}
 	}
}