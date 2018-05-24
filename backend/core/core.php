<?php

$core = core::getInstance();

class core
{

    private $require = array();

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    //Загрузить всё едро и компоненты
    public function init()
    {
        global $config;
        if ($config['debug']) return $this->init_debug();
            return $this->init_production();

    }

    //Инициализация ядра [ боевой режим ]
    private function init_production()
    {

        global $config;

        //Подключаем необходимые модули
        foreach( array('core','components') as $dir ) {

            $dir = $_SERVER['DOCUMENT_ROOT'] . '/backend/' . $dir;
            foreach( scandir($dir) as $file) {
                if ( mb_substr($file,-4) != '.php' )
                    continue;

                $this->require[$dir . '/' . $file] = mb_substr($file,0,-4);
                require_once $dir . '/' . $file;
            }

        }

        //Подключаем шаблонизатор
        $component = $_SERVER['DOCUMENT_ROOT'].'/backend/core/Twig/Autoloader.php';
        require_once($component);

        //Проверяем запрос на внешний API или нет
        $api = false;
        if( $config['open_api'] )
            if (mb_substr($_SERVER['REQUEST_URI'], 0, 5) == '/api/')
                $api = true;

        //Если запрос на API
        if( $api === true ){

            $api = api::getInstance();
            $api_method = mb_substr($_SERVER['REQUEST_URI'],4);
            $api_data = $this->getAPIData();
            echo $api->{$api_data['method']}($api_method, $api_data['data']);
            exit;

        //Если запрос на контроллер
        } else {

            //Инициализируем контроллер
            $view_data = $controller->init();

            //Выводим шаблон
            Twig_Autoloader::register();
            $loader = new Twig_Loader_Filesystem($config['templates_dir']);
            $twig_settings = array(
                'cache'       => false,
                'auto_reload' => true
            );
            $twig = new Twig_Environment($loader, $twig_settings);
            echo $twig->render($view_data['template'],$view_data['vars']);

        }

    }


    //Инициализация ядра [ тестовый режим ]
    private function init_debug(){

        global $config;

        //Подключаем необходимые модули
        foreach( array('core','components') as $dir ){

            $dir = $_SERVER['DOCUMENT_ROOT'].'/backend/'.$dir;
            foreach( scandir($dir) as $file) {
                if ( mb_substr($file,-4) != '.php' )
                    continue;

                $this->require[$dir.'/'.$file] = mb_substr($file,0,-4);
                require_once $dir.'/'.$file;
            }

        }

        //Подключаем шаблонизатор
        $component = $_SERVER['DOCUMENT_ROOT'].'/backend/core/Twig/Autoloader.php';
        require_once($component);

        //Проверяем запрос на внешний API или нет
        $api = false;
        if( $config['open_api'] )
            if (mb_substr($_SERVER['REQUEST_URI'], 0, 5) == '/api/')
                $api = true;


        //Если запрос на API
        if( $api === true ){

            $api = api::getInstance();
            $api_method = mb_substr($_SERVER['REQUEST_URI'],4);
            $api_data = $this->getAPIData();
            echo $api->{$api_data['method']}($api_method,$api_data['data']);
            exit;


        //Если запрос на контроллер
        }else{

            //Инициализируем контроллер
            $view_data = $controller->init_debug();

            //Выводим шаблон
            Twig_Autoloader::register();
            $loader = new Twig_Loader_Filesystem($config['templates_dir']);
            $twig_settings = array(
                'cache'       => false,
                'auto_reload' => true
            );
            $twig_settings['debug'] = true;
            $twig = new Twig_Environment($loader,$twig_settings);
            $twig->addExtension(new Twig_Extension_Debug());
            echo $twig->render($view_data['template'],$view_data['vars']);

        }

        //Записываем информацию в debug
        $this->putDebug();
    }


    //Получение данных из тела запроса
    function getAPIData()
    {

        $method = mb_strtolower($_SERVER['REQUEST_METHOD']);

        if ($method === 'get') return array('method' => 'get', 'data' => $_GET);
        if ($method === 'post') return array('method' => 'post', 'data' => $_POST);

        $data = file_get_contents('php://input');
        $data = json_decode($data,1);

        if( $method != 'put' && $method != 'delete' )
            $method = 'get';

        return array('method' => $method, 'data' => $data);
    }


    //Получить подключенные модули
    public function getRequire(){
        return $this->require;
    }


    //Записываем информацию в debug
    public function putDebug(){

        $db = db::getInstance();
        $controller = controller::getInstance();

        $array = array();
        $array['db_stats'] = $db->getStats();
        $array['page'] = $_SERVER['REQUEST_URI'];
        $array['controller'] = $controller->this_file();
        $array['create_time'] = time();

        if( $array['controller'] != '_dev/controllers/index.php' ):

            $file = $_SERVER['DOCUMENT_ROOT'].'/_dev/files/debug.txt';
            if( is_file($file) ){
                $data = file_get_contents($file);
                $data = json_decode($data,1);

                if( !is_array($data) )
                    $data = array();

            }else{
                $data = array();
            }

            array_unshift($data,$array);
            $data = json_encode($data);

            $fp = fopen($file, "w");
            fwrite($fp, $data);
            fclose($fp);

        endif;

    }


}

?>