<?php

if (empty($_SESSION['client']['status'])) {
    header('location:' . $_SESSION['langlink'].'/cabinet/login/');
    exit;
}

$user = $api->get('/cabinet/',$_SESSION['client']);
$_SESSION['client'] = $user;

/*
* 0 - клиент
* 1 - менеджер
* 2 - партнер
*/
$user_role=$user['role'];

if(!$user_role) {
 $template = 'cabinet/bonus.html';
 
    if ($this->GET[0] == 'bonus') {
        $template = 'cabinet/bonus.html';
    }

    if ($this->GET[0] == 'history') {
        $template = 'cabinet/history.html';
    }

    if ($this->GET[0] == 'order' && $this->GET[1] > 0) {
        $template = 'cabinet/order.html';
    }

    $vars['cars'] = $api->get('/cabinet/cars_client/',$_SESSION['client']['id']);
    //список заявок клиента
    $vars['history'] = $api->get('/cabinet/history/',$_SESSION['client']['id']);
    $vars['order'] = $api->get('/cabinet/order/',$this->GET[1]);
    //сверяем ID клиента заявки и авторизированного
    if($vars['order']['id_client']!=$_SESSION['client']['id']) {
        unset($vars['order']);
        $vars['order']="Заявка не Ваша. Оперируйте своими";
    }

} elseif($user_role == 1) {
    //карточка заказа
    if ($this->GET[0] == 'order' && $this->GET[1] > 0) {
        $template = 'cabinet/order.html';
        $vars['order'] = $api->get('/cabinet/orderManager/',$this->GET[1]);
        $template = 'cabinet/manager/order.html';
        //иначе весь список
    } else {
        $vars['history'] = $api->get('/cabinet/historyManager/');
        $template = 'cabinet/manager/index.html';
    }

} elseif ($user_role==2) {
    echo "партнер";
     $template = 'cabinet/partner/index.html';
}

if(true) {
    //категории и виды работ
    $all_cats=$settings->GetAllCategorys_orders($config['lang']); //для вывода категорий и работ в формах
    foreach($all_cats as $key) {
        $vars['workCategory'][$key['id']]=$key;
        if($key['child']) {
            foreach($key['child']  as  $chkey)
                $vars['workType'][$key['id']][$chkey['id']]=$chkey;
        }
    }
}

$template = 'cabinet/index.html';
