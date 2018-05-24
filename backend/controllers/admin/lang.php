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
	if( isset($this->GET[0]) && $this->GET[0] == 'ajax_mo_update' ){
		if( !empty($_POST['ajax']) && !empty($_POST['sname']) ) {
			if($time=$settings->update_mo($_POST)){
				exit('ok');
			}
		}
		exit;
	}
    if(isset($row)) $vars['row']=$row;

?>
