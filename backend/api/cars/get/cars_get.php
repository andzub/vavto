<?php
$cars_get = cars_get::getInstance();
class cars_get {
    
    protected static $_instance;
    private function __clone() {}
    //private function __wakeup() {}
    private function __construct() {}
    
    public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
    }

    public function brend($data)
    {
        $db = db::getInstance();
        $brend = $db->getAll('SELECT * FROM `car_mark` WHERE `car_mark`.`name` LIKE ?s LIMIT 20','%'.$data['id'].'%');
        return $brend;
    }

    //список марок авто в личном кабинете с учетом уже добавленных авто
    public function brendcl($data)
    {

        $db = db::getInstance();
        //получить ИД клиента
        $clid=$_SESSION['client']['id'];
        //получить машини юзера
        $cl_cars = $db->getAll('SELECT  car_mark.*
        FROM `cars_clients`
        INNER JOIN `car_mark` ON `cars_clients`.`autoBrand_id`=`car_mark`.`id`
        WHERE `cars_clients`.`client_id` = ?i order by id',(int)$clid);

        //получаем список ИД добавленных клиентом авто
        foreach($cl_cars as $key=>$v)
            $cl_cars_list[]=$v['id'];
            //получаем список всех авто по запросу
        $brend = $db->getAll('SELECT * 
        FROM `car_mark` 
        WHERE `car_mark`.`name` LIKE ?s       
        LIMIT 20','%'.$data['id'].'%');


        if($data['id']) return $brend; // с поиском - вернуть найденное

        //иначе убираем дубли
        foreach($brend as $k=>$v)
            if(in_array($v['id'],$cl_cars_list )) unset($brend[$k]);
        $res=array_merge_recursive ($cl_cars, $brend );
        return $res;
    }

    public function model($data)
    {
        $db = db::getInstance();
        $model = $db->getAll('SELECT * FROM `car_model` WHERE `car_model`.`brand_id` = ?i 
            AND `car_model`.`name` LIKE ?s LIMIT 20',$data['brend_id'],'%'.$data['q'].'%');
        return $model;
    }

    //список моделей с учетом клиентских добавленых
    public function modelcl($data)
    {
        $db = db::getInstance();
        $clid=$_SESSION['client']['id'];

        //получить модели машин юзера
        if($clid) $cl_cars = $db->getAll('SELECT  car_model.*
        FROM `cars_clients`
        INNER JOIN `car_model` ON `cars_clients`.`autoModel_id`=`car_model`.`id`
        WHERE `cars_clients`.`client_id` = ?i  
        order by `car_model`.id',(int)$clid );
        else $cl_cars = $db->getAll('SELECT  car_model.*
        FROM `cars_clients`
        INNER JOIN `car_model` ON `cars_clients`.`autoModel_id`=`car_model`.`id`
        order by `car_model`.id' );

        //получаем список ИД добавленных клиентом авто (модели) 
        foreach($cl_cars as $key=>$v)
        {
            if($v['brand_id'] != $data['brend_id']) unset($cl_cars[$key]);
            else $cl_cars_list[]=$v['id']; // выбираем модели только указаной марки
        }

        $model = $db->getAll('SELECT * FROM `car_model` 
            WHERE `car_model`.`brand_id` = ?i 
            AND `car_model`.`name` LIKE ?s LIMIT 20',$data['brend_id'],'%'.$data['q'].'%');

        if($data['q']) return $model; // с поиском - вернуть найденное
        //иначе убираем дубли
        foreach($model as $k=>$v)
            if(in_array($v['id'],$cl_cars_list )) unset($model[$k]);
        $res=array_merge_recursive ($cl_cars, $model );
        return $res;
    }


}