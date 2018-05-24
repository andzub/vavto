<?php
        
    $users_get = users_get::getInstance();
    class users_get {
    
        protected static $_instance;
        private function __clone() {}
        private function __wakeup() {}
        private function __construct() {}
    
        public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
        }
    
        public function usual($data) //получить юзеров
        {

            $user_types=array(0=>"Клиент", 1=>"Менеджер", 2=>"Партнер");
            $db = db::getInstance();
            $row= $db->getAll('SELECT client.*, geo_city.name as city
            FROM `client`
            left join geo_city  ON geo_city.id=client.city_id
            ORDER BY client.`id` DESC');

                $res=array();
                foreach ($row as $key)
                {
                    $res[$key['id']]=$key;
                    $res[$key['id']]['roletext']=$user_types[$key['role']];
                }


                return $res;
        }

        public function getuser($data)
        {
            $db = db::getInstance();
            $sql ='SELECT client.*,    geo_city.name as city
                    FROM `client`
                    left join geo_city  ON geo_city.id=client.city_id 
                    where client.id=?i';
            $user =  $db->getAll( $sql , $data['id'] );
            return $user;
        }

      /*  public function limm($data)
        {
            $db = db::getInstance();
            $colacii = 3;
            $offset = $data['id'] * $colacii;
            $sql='SELECT * FROM `stocks` WHERE `stocks`.`id` IN(SELECT `stocks_id` FROM `city_stocks` WHERE `city_stocks`.`city_id` = '.$_SESSION['city']['id'].') ORDER BY `id` DESC LIMIT '.$colacii.' OFFSET '.$offset;
            $acii = $db->getAll($sql);
            return $acii;
        }

        public function index($data)
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `stocks` WHERE lang=?s ORDER BY RAND() LIMIT 3',$data['lang']);
        }*/
    
    
    }
 