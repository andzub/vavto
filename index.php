<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once('config.php');
require_once('backend/core/core.php');
$core->init();

/*if( $config['protocol'] == 'https' ){
    if($_SERVER['SERVER_PORT'] != '443'){
        header('Location: https://'.$config['domain'].$_SERVER['REQUEST_URI']);
        exit;
    }
}*/

