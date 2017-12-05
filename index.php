<?php 
	include('classes/DB.php');
	include ('classes/Login.php');

	if (Login::isLoggedIn()) {
		echo 'User logged in';
		echo Login::isLoggedIn();
	}else{
		echo 'User not logged in';
	}

 ?>