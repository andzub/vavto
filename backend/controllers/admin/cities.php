<?php
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
if( $this->GET[0] === 'lang' ){
	if( $_POST['ajax'] ){
		$response = $api->put('/cities/change_lang_val/',$_POST);
		if($response) echo 'ok';
		exit;
	}
	if(empty($this->GET[1])) return false;
	$vars['page'] = (int)$this->GET[2] ?: 1;
	//echo "<pre>".print_r($this->GET,true)."</pre>";exit;

	$page_data=$api->get('/cities/lang/',['lang'=>$this->GET[1],'page'=>$vars['page']]);
	$vars['rows'] = $page_data['rows'];
	$vars['pgn_count']=$page_data['pgn_count'];
	$vars['city_lang']=$this->GET[1];
	$vars['filter']=isset($_POST['filter']) ? $_POST['filter'] : [];
	$template = 'cities-lang.html';
}
else {
    $vars['regions'] = $api->get('/cities/regions/');
    $vars['rows'] = $api->get('/cities/', $this->GET);
	$vars['page'] = (int)$this->GET[1] ?: 1;
    $vars['region'] = $this->GET[0] ?: 'all';
}
?>
