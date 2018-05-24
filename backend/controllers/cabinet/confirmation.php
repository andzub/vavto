<?php
if (!isset($_SESSION['client']['id'])) {
    header('location:' . $_SESSION['langlink'].'/cabinet/login/');
    exit;
}
if (!empty($_SESSION['client']['status'])) {
    header('location:' . $_SESSION['langlink'].'/cabinet/');
    exit;
}

if (isset($this->GET[0]) && $_SESSION['client']['id'] == $this->GET[0]) {
    $response = $api->put('/cabinet/status/',$this->GET[0]);

    $user = $api->get('/cabinet/',$_SESSION['client']);
    
    $_SESSION['client'] = $user;
    header('location:' .  $_SESSION['langlink'].'/cabinet/');
    exit;
}

