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
    $user_types=array(0=>"Клиент", 1=>"Менеджер", 2=>"Партнер");

    if( !$this->GET[0]) //редирект на all (список юзеров)
    {
        header('Location: /admin/users/all');
        exit;
    
    }
    elseif( $this->GET[0] == 'add' ){

        if( $_POST['ajax'] ){

            //$_POST['img'] = $_FILES['img'];
            //$_POST['logo'] = $_FILES['logo'];
            $response = $api->post('/users/',$_POST);
            echo json_encode($response);
            exit;

        }

        $template = 'user-add.html';

    }

    elseif( $this->GET[0] == 'edit' ){

        if( $_POST['ajax'] ){ //записать
            //$_POST['img'] = $_FILES['img'];
            //$_POST['img_min'] = $_FILES['img_min'];
            $response = $api->put('/users/',$_POST);
            echo json_encode($response);
            exit;
        }


        // вернуть
        $row = $api->get('/users/getuser/', $this->GET[1]);
        //$row['time'] = date('Y-m-d', $row['time']);
        if( !$row ){
            header('Location: /admin/users/');
            exit;
        }
        $vars['row']=$row[0];
        foreach ($user_types as $key=>$val) $vars['rolelist'][$key]=$val; //список ролей
        $template = 'user-edit.html';
        //var_dump($vars);
    }

    elseif( $this->GET[0] == 'delete' ){
        $api->delete('/users/',$this->GET[1]);
        header('Location: /admin/users/all/');
        exit;
    }
    else //список юзеров
    {
        $vars['rows'] = $api->get('/users/all', $this->GET[1]);
        $template = 'users.html';
    }

    //$vars['row']=$row;

?>
