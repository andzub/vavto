<?php

$cabinet_get = cabinet_get::getInstance();

class cabinet_get
{

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance()
    {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    public function usual($data)
    {
        $db = db::getInstance();

        $sql = 'SELECT `client`.* FROM `client`  WHERE `client`.`id` = ?i';
        $user = $db->getRow($sql,$data['id']);

        $sql = 'SELECT * FROM `geo_city`  WHERE `id` = ?i';
        $city = $db->getRow($sql,$user['city_id']);

        $date = explode("-", $user["date"]);
        $mass[] = $date[1];
        $mass[] = $date[2];
        $mass[] = $date[0];

        $date = implode("-", $mass);
        $user['dates'] = $date;
        $user['city'] = $city['name'];
        return $user;
    }

    public function client($data)
    {
        $db = db::getInstance();
        $user = $db->getRow('SELECT * FROM `client` WHERE  `status`= 1 AND`client`.`email` = ?s',$data['email']);
        return $user;
    }

    public function cars_client($data)
    {
        $db = db::getInstance();
        $sql = 'SELECT `cars_clients`.*, `car_mark`.`name` as namebrend, `car_model`.`name` as namemodel ,
        `car_mark`.img as carlogo
        FROM `cars_clients`
        INNER JOIN `car_mark` ON `cars_clients`.`autoBrand_id`= `car_mark`.`id` 
        INNER JOIN `car_model` ON `cars_clients`.`autoModel_id`=`car_model`.`id` 
        WHERE `cars_clients`.`client_id` = ?i';

        $cars = $db->getAll($sql,$data['id']);
        return $cars;
    }

    //получить историю заявок для клиента \backend\controllers\cabinet\index.php
    //добавляются заявки settings.php
    public function history()
    {
        $db = db::getInstance();
        $clid=$_SESSION['client']['id'];

        $status_arr=array(
            1=> "<span style='color:green'>Новый</span>",
            2=> "<span style='color:#4a8dc6'>В работе</span>",
            3=> "<span style='color:blue'>Выполнен</span>",
            4=> "<span style='color:red'>Отменен</span>",
        );

        $sql = 'SELECT history.*, cl1.title as subcat , cl2.title as parcat
        from history
        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"
        
        WHERE `id_client` = ?i 
        order by date_add desc';

        $history = $db->getAll($sql,$clid);

        //меняем даты и время  и другое..
        foreach($history as $key=>$v) {
            if($v['date_add'])
                $history[$key]['date_add'] = date( "d-m-Y G:i", $v['date_add'] );
            else  $history[$key]['date_add']='-';

            if($v['date_udobno'])
                $history[$key]['date_udobno'] = date( "d-m-Y G:i", $v['date_udobno'] );
            else  $history[$key]['date_udobno']='-';

            $history[$key]['problem']="".nl2br( $v['problem'] );
            $history[$key]['status']=$status_arr[$v['status']] ;

            if($v['parcat'])$tmparr[]=$v['parcat'];
            if($v['subcat'])$tmparr[]=$v['subcat'];
            $history[$key]['cats']=implode(" / ",$tmparr); // категорию и вид работв одну строку
            if(!$history[$key]['cats']) $history[$key]['cats']='-';
            unset($tmparr);
        }
        return $history;
    }


    //получить карточку заказа
    public function order($idorder)
    {


        $status_arr=array(
            1=> "<span style='color:green'>Новый</span>",
            2=> "<span style='color:#4a8dc6'>В работе</span>",
            3=> "<span style='color:blue'>Выполнен</span>",
            4=> "<span style='color:red'>Отменен</span>",
        );

        //$status_arr= array(1=>'Новый', 2=>'В работе', 3=>'Выполнен', 4=>'Отменен');

        $db = db::getInstance();
        $idorder=$idorder['id'];
        //echo $idorder;

        $sql = 'SELECT history.*, car_mark.name as car_marka_name,  car_model.name as car_model_name,
        cl1.title as subcat , history.category as subcatid,
        cl2.title as parcat , cats.parent_id as parcatid
        
        from history

        left join car_mark on car_mark.id=history.car_marka
        left join car_model on car_model.id=history.car_model
        
        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"
        
        
        WHERE history.`id` = ?i';


       /* $sql = 'SELECT history.*, cl1.title as subcat , cl2.title as parcat
        from history
        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"
        ';*/

        $order = $db->getRow($sql,$idorder);

        $order['statustext']=$status_arr[$order['status']]; //статус текстом
        $order['date_add']=date( "d-m-Y G:i",$order['date_add']); //формат дат
        $order['date_udobno']=date( "d-m-Y G:i",$order['date_udobno']); //формат дат

        if($order['parcatid']) $order['type']=2;
        else {$order['type']=1; $order['parcatid']=0;}

        $order['stlist']=$status_arr;



        /*echo "<pre>";
        var_dump($order);
        echo "</pre>";*/
        //var_dump($order);
        return $order;
    }


/*-------------------------------ЛК менеджера --------*/
     //получить историю заявок для клиента \backend\controllers\cabinet\index.php
    //добавляются заявки settings.php
    public function historyManager()
    {
        $db = db::getInstance();
        //$clid=$_SESSION['client']['id'];

        $status_arr=array(
            1=> "<span style='color:green'>Новый</span>",
            2=> "<span style='color:#4a8dc6'>В работе</span>",
            3=> "<span style='color:blue'>Выполнен</span>",
            4=> "<span style='color:red'>Отменен</span>",
        );


        $sql = 'SELECT history.*, cl1.title as subcat , cl2.title as parcat
        from history
        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"


        order by date_add desc';


        $history = $db->getAll($sql);

        //меняем даты и время  и другое..
        foreach($history as $key=>$v)
        {
            if($v['date_add'])
                $history[$key]['date_add'] = date( "d-m-Y G:i", $v['date_add'] );
            else  $history[$key]['date_add']='-';

            if($v['date_udobno'])
                $history[$key]['date_udobno'] = date( "d-m-Y G:i", $v['date_udobno'] );
            else  $history[$key]['date_udobno']='-';

            $history[$key]['problem']="".nl2br( $v['problem'] );
            $history[$key]['status']=$status_arr[$v['status']] ;

            if($v['parcat'])$tmparr[]=$v['parcat'];
            if($v['subcat'])$tmparr[]=$v['subcat'];
            $history[$key]['cats']=implode(" / ",$tmparr); // категорию и вид работв одну строку
            if(!$history[$key]['cats']) $history[$key]['cats']='-';
            unset($tmparr);
        }
        return $history;
    }


    //получить карточку заказа
    public function orderManager($idorder)
    {


        $status_arr=array(
            1=> "<span style='color:green'>Новый</span>",
            2=> "<span style='color:#4a8dc6'>В работе</span>",
            3=> "<span style='color:blue'>Выполнен</span>",
            4=> "<span style='color:red'>Отменен</span>",
        );

        //$status_arr= array(1=>'Новый', 2=>'В работе', 3=>'Выполнен', 4=>'Отменен');

        $db = db::getInstance();
        $idorder=$idorder['id'];
        //echo $idorder;

        $sql = 'SELECT history.*, car_mark.name as car_marka_name,  car_model.name as car_model_name,
        cl1.title as subcat , history.category as subcatid,
        cl2.title as parcat , cats.parent_id as parcatid

        from history

        left join car_mark on car_mark.id=history.car_marka
        left join car_model on car_model.id=history.car_model

        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"


        WHERE history.`id` = ?i';


       /* $sql = 'SELECT history.*, cl1.title as subcat , cl2.title as parcat
        from history
        left join cats on cats.id=history.category
        left join cats_lang cl1 on cl1.cat_id=history.category AND cl1.lang="ru"
        left join cats_lang cl2 on cl2.cat_id=cats.parent_id AND cl2.lang="ru"
        ';*/

        $order = $db->getRow($sql,$idorder);

        $order['statustext']=$status_arr[$order['status']]; //статус текстом
        $order['date_add']=date( "d-m-Y G:i",$order['date_add']); //формат дат
        //$order['date_udobno']=date( "d-m-Y G:i",$order['date_udobno']); //формат дат

        $order['time_udobno']=date( "H:i",$order['date_udobno']); //формат времени
        $order['date_udobno']=date( "d.m.Y",$order['date_udobno']); //формат дат

        if($order['parcatid']) $order['type']=2;
        else {$order['type']=1; $order['parcatid']=0;}

        $order['stlist']=$status_arr;



        /*echo "<pre>";
        var_dump($order);
        echo "</pre>";*/
        //var_dump($order);
        return $order;
    }

}