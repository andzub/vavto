<?php

$token = token::getInstance();
class token {

    private $token = false;

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}
    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    //Получить токет
    public function get(){

        if( $this->token )
            return $this->token;

        $token = md5(uniqid(rand(),1));
        $_SESSION['token'] = $token;
        $this->token = $token;
        return $this->token;

    }

    //Проверить на правильность
    public function check($check_token){return true;

        if( !$_SESSION['token'] )
            return false;

        if( empty($check_token) )
            return false;

        if( $check_token === $_SESSION['token'] ){
            $this->token = $check_token;
            return true;
        }

        return false;

    }


}

?>