<?php
	if (empty($_POST['id']) || empty($_POST['title']) || !preg_match('/^-?\d+$/', $_POST['id'])) {
		exit('error');
	}
	header('Content-type: application/json');
	require 'bootstrap.php';
	$response = array(
		'err_code' => 1
	);
	if (dibi::query('UPDATE `items` SET ', array('title' => $_POST['title']), 'WHERE `id`=%i', $_POST['id'])) {
		$response = array('err_code' => 0);
	}
	echo json_encode($response);
