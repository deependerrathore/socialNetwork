<?php 

	require_once('DB.php');

	$db = new DB("127.0.0.1","SocialNetwork","root","admin");
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		echo 'POST request';
	}else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		echo json_encode($db->query('SELECT * FROM users'));
		http_response_code(200);
	}else{
		http_response_code(405);
	}
 ?>