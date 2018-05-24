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
            $response = $api->post('/new/',$_POST);
            echo json_encode($response);
            exit;
        }
        $template = 'news-add.html';
    }
    if( $this->GET[0] == 'edit' ){
        if( $_POST['ajax'] ){
            $_POST['img'] = $_FILES['img'];
            $_POST['img_min'] = $_FILES['img_min'];
            $response = $api->put('/new/',$_POST);
            echo json_encode($response);
            exit;
        }
	    if(!empty($this->GET[2]) && isset($config['langs'][$this->GET[2]])) {
		    $cur_lang=$this->GET[2];
		    $get_data=['id'=>$this->GET[1],'lang'=>$this->GET[2]];
	    }
	    else {
		    $cur_lang='ru';
		    $get_data=['id'=>$this->GET[1],'lang'=>'ru'];
	    }
        $row = $api->get('/new/', $get_data);
        if( !$row ){
            header('Location: /admin/news/');
            exit;
        }
	    $row['cur_lang']=$cur_lang;
        $template = 'news-edit.html';
    }

    if( $this->GET[0] == 'delete' ){
        $api->delete('/new/',$this->GET[1]);
        header('Location: /admin/news/');
        exit;
    }
    $vars['rows'] = $api->get('/new/all/');
    $vars['row'] = $row;
?>
