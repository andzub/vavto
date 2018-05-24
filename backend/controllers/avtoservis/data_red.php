<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$data = $api->put('/avtoservis/',$_POST);
	echo json_encode($data);
    exit;
}

$avtoservis = $api->get('/avtoservis/',$_SESSION);

$_SESSION['avtoservis'] = $avtoservis;