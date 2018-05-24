<?php

if (empty($_SESSION['client']['status'])) {
    header('location:' . $_SESSION['langlink'].'/cabinet/login/');
    exit;
}
//var_dump($_SERVER);
/*
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

	if ($this->GET[0]) {


	}

	if ($this->GET[0] == 'pass_reset') {

		$data = $api->put('/cabinet/pass_reset/',$_POST);
		echo json_encode($data);
    	exit;
	}

	if ($this->GET[0] == 'personal') {
		$data = $api->put('/cabinet/data/',$_POST);
		echo json_encode($data);
    	exit;
	}

	if ($this->GET[0] == 'car_add') {
		$data = $api->post('/cabinet/car_add/',$_POST);
		echo json_encode($data);
    	exit;
	}

	if ($this->GET[0] == 'car_update') {
		$data = $api->put('/cabinet/car_update/',$_POST);
		echo json_encode($data);
    	exit;

	}

}*/

$user = $api->get('/cabinet/',$_SESSION['client']);
$_SESSION['client'] = $user;
//$vars['myorders'] = $api->get('/cabinet/history/',$_SESSION['client']['id']);
