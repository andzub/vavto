<?php

    global $config;

    if( !$config['debug'] ){
        $controller->init('404');
        exit;
    }

    if( $_POST['method'] == 'createObject' ):
        createObject($_POST['name']);
        header('Location: /_debug/');
    endif;

    $objects = getObjects();
    $methods = getMethods('get',$objects[0]);
    $pages_load = getLogs();

?>
<!doctype html>
<html lang=ru>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Debug</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/superhero/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>

    <style>
        .query-red {color:red;}
        .form-control {background:#f6f6f6;color:#000;}
        .form-control::-webkit-input-placeholder {color:#7a7a7a;}
        .form-control::-moz-placeholder          {color:#7a7a7a;}
        .form-control:-moz-placeholder           {color:#7a7a7a;}
        .form-control:-ms-input-placeholder      {color:#7a7a7a;}
    </style>
</head>
<body>

    <br>

    <div class="col-md-7">
        <div class="panel">
            <div class="panel-body">
                <form action="" method="POST">
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control" name="type">
                                <option value="get">GET</option>
                                <option value="post">POST</option>
                                <option value="put">PUT</option>
                                <option value="delete">DELETE</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="object">
                                <? foreach( $objects as $row ): ?>
                                    <option value="<?=$row?>"><?=$row?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="method">
                                <? foreach( $methods as $row ): ?>
                                    <option value="<?=$row?>"><?=$row?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-control" type="text" name="method" placeholder="Введите запрос">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input class="form-control" type="text" name="data" placeholder="Данные">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary">Отправить</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                test
            </div>
        </div>

    </div>

    <div class="col-md-5">
        <div class="panel">
            <div class="panel-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <input class="form-control" type="text" name="name" placeholder="Название объекта, например user" autocomplete="off">
                    </div>
                    <button class="btn btn-primary">Создать объект API</button>
                    <input type="hidden" name="method" value="createObject">
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <input class="form-control" type="text" name="name" placeholder="Название компонента, например extra">
                    </div>
                    <button class="btn btn-primary">Создать компонент</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <form action="" method="POST">
                    <button class="btn btn-danger">Очистить лог</button>
                </form>
            </div>
        </div>
        <?=$pages_load?>

    </div>

<script type="text/javascript">
$(document).ready(function () {

    $('select').select2();

});
</script>

</body>
</html>

<?php

    //Получить список объектов
    function getObjects(){

        $dir = $_SERVER['DOCUMENT_ROOT'].'/backend/api/';
        $files = scandir($dir);
        $array = array();
        foreach($files as $file) {
            if (($file !== '.') AND ($file !== '..')){
                $array[] = $file;
            }
        }

        return $array;
    }

    //Получить список методов
    function getMethods($type,$object){

        $class_name = $object.'_'.$type;
        $file = $_SERVER['DOCUMENT_ROOT'].'/backend/api/'.$object.'/'.$type.'/'.$class_name.'.php';

        if( !file_exists($file) )
            return array();

        require_once($file);
        $class = $class_name::getInstance();
        $class_methods = get_class_methods(get_class($class));

        $array = array();
        foreach ($class_methods as $method){
            if( $method == 'getInstance' )
                continue;

            $array[] = $method;
        }

        return $array;

    }

    //Создать объект
    function createObject($name){

        $dir = $_SERVER['DOCUMENT_ROOT'].'/backend/api/'.$name.'/';
        if( file_exists($dir) )
            return false;

        mkdir($dir);
        $array = array('get','post','put','delete');

        foreach($array as $type ):

            $text_file = '<?php
    
$'.$name.'_'.$type.' = '.$name.'_'.$type.'::getInstance();
class '.$name.'_'.$type.' {

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    public function usual($data)
    {
        return json_encode($data);
    }


}
    
?>';

            mkdir($dir.$type.'/');
            file_put_contents($dir.$type.'/'.$name.'_'.$type.'.php', $text_file);

        endforeach;

        return true;
    }


    //Получить HTML логов
    function getLogs(){

        $file = $_SERVER['DOCUMENT_ROOT'].'/backend/debug/debug.txt';
        if( is_file($file) ){
            $data = file_get_contents($file);
            $data = json_decode($data,1);
        }else{
            $data = array();
        }

        $pages_load = '';
        foreach( $data as $key => $row ){

            if( $key > 99 )
                break;

            $pages_load .= '<div class="panel"><div class="panel-body"> ';
            $pages_load .= '<p>Страница: <span>'.$row['page'].'</span></p>';
            $pages_load .= '<p>Контроллер: <span>'.$row['controller'].'</span></p>';
            $pages_load .= '<p><span>Запросы к БД ('.count($row['db_stats']).'):</span></p>';
            if( is_array($row['db_stats']) ) {
                foreach ($row['db_stats'] as $query) {

                    if( $query['timer'] > 0.05 ){
                        $red_query = 'query-red';
                    }else{
                        $red_query = '';
                    }

                    $pages_load .= '<p class="query '.$red_query.'">' . $query['query'] . ' | ' . $query['timer'] . '</p>';
                }
            }else{
                $pages_load .= '<p class="query">Нет запросов.</p>';
            }

            $pages_load .= '<p>Время записи в лог: <span>'.date('d.m.Y \в H:i:s',$row['create_time']).'</span></p>';

            $pages_load .= '</div></div>';
        }

        return $pages_load;
    }


?>
