<?php 
	if (isset($_POST['uploadprofileimg'])) {
		$image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name'])) ;

		$options = array('http'=>array(
			'method' => "POST",
			'header' => "Authorization: Bearer 1a3c3731ab5656d3342d8dc713f89d09f46d977a\n".
			"content-type: application/x-www-form-urlencoded",
			'content' => $image
		));

		$context = stream_context_create($options);

		$imageURL = 'https://api.imgur.com/3/image';
		$response = file_get_contents($imageURL,false,$context);

	}
 ?>
<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
	Upload a profile image:
	<input type="file" name="profileimg">
	<input type="submit" name="uploadprofileimg" value="Upload Image">
</form>