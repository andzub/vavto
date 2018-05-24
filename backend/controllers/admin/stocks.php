<?php

    //Пароль
    global $config;
    if ( !isset($_SERVER['PHP_AUTH_USER'] )) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Ошибка доступа!';
        exit;
    } else {

        if( ($_SERVER['PHP_AUTH_USER'] != $config['admin_login'] || $_SERVER['PHP_AUTH_PW'] != $config['admin_pass']) && $_COOKIE['admin_auth'] != md5($config['admin_login'].$config['admin_pass']) ){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Ошибка доступа!';
            exit;
        }else{

            $time_cookies = mktime(0,0,0,1,1,2030);
            setcookie('admin_auth', md5($config['admin_login'].$config['admin_pass']), $time_cookies, "/", ".".$config['domain']);

        }

    }

    if( $this->GET[0] == 'add' ){

        if( $_POST['ajax'] ){

            $_POST['img'] = $_FILES['img'];
            $_POST['logo'] = $_FILES['logo'];
            $response = $api->post('/stocks/',$_POST);
            echo json_encode($response);
            exit;

        }

        $template = 'stocks-add.html';

    }

    if( $this->GET[0] == 'edit' ){

        if( $_POST['ajax'] ){

            $_POST['img'] = $_FILES['img'];
            $_POST['logo'] = $_FILES['logo'];
            $response = $api->put('/stocks/',$_POST);
            echo json_encode($response);
            exit;

        }

        $row = $api->get('/stocks/', $this->GET[1]);
        $row['time'] = date('Y-m-d', $row['time']);
        if( !$row ){
            header('Location: /admin/stocks/');
            exit;
        }

        $template = 'stocks-edit.html';

    }

    if( $this->GET[0] == 'delete' ){
        $api->delete('/stocks/',$this->GET[1]);
        header('Location: /admin/stocks/');
        exit;
    }



    if( $this->GET[0] == 'citydel' ){
        $api->delete('/citystocks/',$this->GET[1]);
        header('location:' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $vars['rows']=$api->get('/stocks/all/');
    $vars['row']=$row;

?>
