<?php
class my_error {
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

            echo '<div style="color: red;padding: 20px;background: #fcefef;border: 1px solid #f6dede;font-size: 16px;font-family: monospace;">'.$error.'</div>';

        }else{
            echo 'Произошла ошибка. Мы уже занимаемся решением проблемы! Извините за доставленые неудобства.';
        }

        exit;
    }
}
$my_error = my_error::getInstance();

?>