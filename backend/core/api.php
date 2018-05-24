<?php

$api = api::getInstance();
class api {

    private $is_controller = false;
    private $check_token = true;

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    //Получить данные
    public function get($url,$data=false)
    {
        $param = $this->parseData($url,$data);
        $response = $this->execute('get',$param);
        return $response;

    }

    //Добавить данные
    public function post($url,$data=false)
    {

        $param = $this->parseData($url,$data);
        $response = $this->execute('post',$param);
        return $response;

    }

    //Изменить данные
    public function put($url,$data=false)
    {

        $param = $this->parseData($url,$data);
        $response = $this->execute('put',$param);
        return $response;

    }

    //Удалить данные
    public function delete($url,$data=false)
    {

        $param = $this->parseData($url,$data);
        $response = $this->execute('delete',$param);
        return $response;

    }


    //Разбор данных
    private function parseData($url,$data){

        $url = explode('/',$url);

        //Если данные не переданы
        if( $data === false ):

            if( count($url) == 3 ):

                $class = $url[1];
                $method = 'usual';
                $data = array(
                    'id' => $url[2]
                );

            else:

                $class = $url[1];
                $method = $url[2];
                $data = array(
                    'id' => $url[3]
                );

            endif;


        //Если данные переданы
        else:

            if( !is_array($data) ):
                $data = array(
                    'id' => $data
                );
            endif;

            if( count($url) == 3 ):

                $class = $url[1];
                $method = 'usual';


            else:

                $class = $url[1];
                $method = $url[2];

            endif;

        endif;

        return array(
            'class' => $class,
            'method' => $method,
            'data' => $data
        );

    }


    //Выполнить запрос
    private function execute($method,$param){

        global $config;

        //Если запрос не из контроллера
        if( $this->is_controller !== true ){

            //Если выключен внешний API
            if( !$config['open_api'] ){

                $response = $this->error(400,'Внешний API выключен.');
                return json_encode($response);

            //Если внешний API включен
            }else{

                $client = $param['data']['client'];
                $key = $param['data']['key'];
                $api_keys = api_keys::getInstance();

                //Если неверные логин и пароль
                if( !$api_keys->is_key($client,$key) ){

                    $response = $this->error(401,'Неверные данные для авторизации.');
                    return json_encode($response);

                }

            }

        }

        //Если токен не верный
        if( $this->check_token !== true ){
            $response = $this->error(401,'Неккоретный токен.');

            if( $this->is_controller !== true )
                $response = json_encode($response);

            return $response;

        }

        //Если все хорошо, запускаем нужный метод
        $class_name = $param['class'].'_'.$method;
        $file = '/backend/api/'.$param['class'].'/'.$method.'/'.$class_name.'.php';

        if( file_exists($_SERVER['DOCUMENT_ROOT'].$file) ){

            require_once($_SERVER['DOCUMENT_ROOT'].$file);
            ${$class_name} = $class_name::getInstance();
            if( method_exists(${$class_name},$param['method']) ){

                $response = ${$class_name}->{$param['method']}($param['data']);
                $response = array(
                    'code' => 200,
                    'data' => $response
                );

            }else{
                $response = $this->error(400,'Некорректный запрос. Метод "'.$param['method'].'" не существует.');
            }

        }else{

            $response = $this->error(400,'Некорректный запрос. Объект "'.$param['class'].'" не существует.');

        }

        //Если запрос из контроллера
        if( $this->is_controller === true ){

            if( $response['code'] == 200 ){
                return $response['data'];
            }else{
                return $response;
            }

        //Если запрос не из контроллера
        }else{

            return json_encode($response);

        }

    }


    //Сформировать ошибку
    private function error($code,$text){
        return array(
            'code' => $code,
            'message' => $text
        );
    }


    //Установить значение is_controller
    public function set_is_controller($value){

        if( $value === true ){
            $this->is_controller = true;
        }else{
            $this->is_controller = false;
        }

    }


    //Установить значение токена
    public function set_check_token($value){

        if( $value === true ){
            $this->check_token = true;
        }else{
            $this->check_token = false;
        }

    }



}

?>