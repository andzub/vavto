<?php

if (empty($_SESSION['client']['status'])) {
    header('location:' . $_SESSION['langlink'].'/cabinet/login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

/*ъышхэЄёъшх ёюїЁрэхэш */
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
	

    //ЛК мен. сохр. заявки
	if ($this->GET[0] == 'saveorder') {
		$data = $api->put('/cabinet/saveorder/',$_POST);
		echo json_encode($data);
    	exit;

	}
	
}

$user = $api->get('/cabinet/',$_SESSION['client']);
$_SESSION['client'] = $user;
$vars['cars'] = $api->get('/cabinet/cars_client/',$_SESSION['client']['id']);

$template = 'cabinet/data.html';