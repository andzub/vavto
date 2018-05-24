<?php

if (isset($_SESSION['client']['id'])) {
    header('location:' . '/cabinet/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($this->GET[0])) {
    $client = $api->get('/cabinet/client/',$_POST);
    if (!empty($client)) {

        $codresset = rand(250, 1000);
        $_SESSION['resetpass']['codresset'] = $codresset;
        $_SESSION['resetpass']['email'] = $client['email'];
        $mail = mail::getInstance();



        $confirmation = "<div style='font-size:16px;'>
        Для смены пароля перейдите по ссылеке <a href='".$_SESSION['langlink']."/cabinet/passresset/".$codresset."/' >
        Смена пароля</a><div>";
        //с учетом языковой версии
        ////        $_SERVER['SERVER_NAME']."/cabinet/passresset/".$codresset."/' >

        $mail->send($client['email'],"avtoservis",$confirmation);

        echo json_encode(array('status'=>true,
            'text'=>'На ваш Email выслано письмо для смены пароля !'));
        exit;
    }
    echo json_encode(array('error'=>true,'text'=>'Такой  email не заригистрирован !'));
    exit;

}

if (!empty($this->GET[0]) && ($this->GET[0] == $_SESSION['resetpass']['codresset'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST['email'] = $_SESSION['resetpass']['email'];
        $data = $api->put('/cabinet/pass_set/',$_POST);
        if (isset($data['status'])) {
            $_SESSION['resetpass'] = [];
        }
        echo json_encode($data);
        exit;
    }
    $template = 'cabinet/passresset-set.html';

}