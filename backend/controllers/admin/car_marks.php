<?php

    //Пароль
    global $config;
    if(!empty($_SERVER['HTTP_AUTHORIZATION'])){//костыль для CGI
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
    }
//echo "<pre>".print_r($_SERVER,true)."</pre>";echo "<pre>".print_r($config,true)."</pre>";die();
    if ( empty($_SERVER['PHP_AUTH_USER'] )) {
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
            $_POST['img_min'] = $_FILES['img_min'];
            $response = $api->post('/car_marks/',$_POST);
            echo json_encode($response);
            exit;

        }

        $template = 'car_marks-add.html';

    }

    if( $this->GET[0] == 'edit' ){

        if( $_POST['ajax'] ){
            $_POST['img'] = $_FILES['img'];
            $response = $api->put('/car_marks/',$_POST);
            echo json_encode($response);
            exit;

        }

        $row = $api->get('/car_marks/', $this->GET[1]);
        if( !$row ){
            header('Location: /admin/car_marks/');
            exit;
        }

        $template = 'car_marks-edit.html';

    }

    if( $this->GET[0] == 'delete' ){
        $api->delete('/car_marks/',$this->GET[1]);
        header('Location: /admin/car_marks/');
        exit;
    }


    $vars = array(
        'rows' => $api->get('/car_marks/all/'),
        'row' => $row
    );

?>
