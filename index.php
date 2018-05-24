<?php


ini_set('display_errors',0);
error_reporting(E_ALL);

//var_dump($_POST);
require_once('config.php');

/*if( $config['protocol'] == 'https' ){
    if($_SERVER['SERVER_PORT'] != '443'){
        header('Location: https://'.$config['domain'].$_SERVER['REQUEST_URI']);
        exit;
    }
}*/

require_once('backend/core/core.php');

$core->init();

