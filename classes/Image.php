<?php 
	
	class Image
	{
		public static function uploadImage($formname,$query,$params){
			$image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name'])) ;

			$options = array('http'=>array(
			'method' => "POST",
			'header' => "Authorization: Bearer 1a3c3731ab5656d3342d8dc713f89d09f46d977a\n".
			"content-type: application/x-www-form-urlencoded",
			'content' => $image
			));

			$context = stream_context_create($options);

			$imageURL = 'https://api.imgur.com/3/image';

			if ($_FILES[$formname]['size'] > 10240000) {
				die('Image too big, must be less 10MB or less!');
			}

			$response = file_get_contents($imageURL,false,$context);
			$response = json_decode($response);
			
			$preparams = array($formname=>$response->data->link);

			$params = $preparams + $params;
			//DB::query('UPDATE users SET profileimg = :profileimg WHERE id=:userid',array(':profileimg'=>$response->data->link,':userid'=>$userid));
			DB::query($query,$params);
		}	
	}
 ?>