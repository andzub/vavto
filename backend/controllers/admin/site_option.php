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

	if( isset($this->GET[0]) && $this->GET[0] == 'edit' ){
        if( !empty($_POST['ajax']) ){
	        $vars['blocks'] = [];
	        $vars['block_names'] = [];
	        $arr_blocks=$api->get('/htmlblock/page_blocks/',$_POST);
	        foreach ($arr_blocks as $block){
	        	/*foreach ($vars['langs'] as $l_key=>$lang){
			        $vars['blocks'][$l_key][$block['name']]=$block['val'];
		        }*/
		        $vars['blocks'][$block['name']][$block['lang']]=$block['val'];
		        $vars['block_names'][$block['name']]='';
		        $vars['page_name']=$_POST['page_name'];
	        }
	        //echo "<pre>".print_r($vars,true)."</pre>";die;
	        $template = 'htmlblock-page_blocks.html';
        }
    }
	$vars['site_options']=$site_options;
?>
