<?php

$errorf = errorf::getInstance();
class errorf {

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    public function _print($error)
    {
        global $config;
        if( $config['debug'] == 1 ){
            echo $error;
        }else{
            echo 'Произошла ошибка. Мы уже занимаемся решением проблемы! Извините за доставленые неудобства.';
        }

        exit;

    }

}

?>