<?php 
	/**
	* 
	*/
	class Notify
	{
		public static function createNotify($text,$postid = 0){

			$notify = array();
			
			if (count($text) != 0) {
				$text = explode(" ", $text);

 				$notify = array();

 				foreach ($text as $word) {
 					if (substr($word,0, 1) == '@') {

 						$username = substr($word, 1);
 						if (DB::query('SELECT username from users WHERE username = :username',array(':username'=>$username))) {
 							$notify[$username] = array("type"=>1,"extra"=>'{"postbody":"'.implode(" ",$text).'"}');
 						}
 				
 					}
 				}	
			}else if (count($text) == 0 && $postid != 0) {
				echo "creating like notification";
 				$temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id AS sender FROM posts , post_likes WHERE posts.id = post_likes.post_id AND posts.id = :postid',array(':postid'=>$postid));
 				$r = $temp[0]['receiver'];
 				$s = $temp[0]['sender'];


 				DB::query('INSERT INTO notifications VALUES (null,:type,:receiver,:sender,:extra)',array(':type'=>2	,':receiver'=>$r,':sender'=>$s,':extra'=>""));
			}
 			

 		return $notify;

 		}
	}
 ?>