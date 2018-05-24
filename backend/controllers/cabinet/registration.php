<?php

if (isset($_SESSION['client']['id'])) {
    header('location:' . $_SESSION['langlink'] . '/cabinet/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$response = $api->post('/cabinet/', $_POST);
	if (isset($response['status'])) {
		$mail = mail::getInstance();

        // Язык EN
		if ($config['lang'] == 'en') {
		    $confirmation = "<div style='font-size:16px;'>
		    To confirm registration, please go to 
		    <a href='" . $_SESSION['langlink'] . '/cabinet/confirmation/' . $response['id'] . "/'>Confirm</a><div>";

		    // Язык RU
        } else {
            $confirmation = "<div style='font-size:16px;'>
		        Для подтверждение регистрации перейдите по сылке 
		        <a href='" . $_SESSION['langlink'] . "/cabinet/confirmation/" . $response['id'] . "/'>Подтвердить</a><div>";
        }


		$mail->send($response['email'],"avtoservis", $confirmation);
		$_SESSION['client']['id'] = $response['id'];
	}

	echo json_encode($response);
	exit;
}
