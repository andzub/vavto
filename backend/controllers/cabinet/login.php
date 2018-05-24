<?php

if (isset($_SESSION['client']['id'])) {
    header('location:' . '/cabinet/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (empty($_POST['email'])) {
        echo json_encode(array('error'=>true,'text'=>'   Укажите email  !'));
        exit;
    }
    if (empty($_POST['pass'])) {
        echo json_encode(array('error'=>true,'text'=>'   Укажите пароль !'));
        exit;
    }
    $client = $api->get('/cabinet/client/',$_POST);
    $password = md5($_POST['pass'] . "avtoservis");
    
    if ($client['password'] == $password) {
        $_SESSION['client'] = $client;
        echo json_encode(array('status'=>true));
        exit;
    } else {
        echo json_encode(array('error'=>true,'text'=>'Неверный email или пароль !'));
        exit;
    }
}



