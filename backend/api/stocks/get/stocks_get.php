<?php
        
    $stocks_get = stocks_get::getInstance();
    class stocks_get {
    
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
            $db = db::getInstance();
            $row = $db->getRow('SELECT * FROM `stocks` WHERE `id`=?i', (int)$data['id']);
            $sql = "SELECT * FROM `city_stocks` INNER JOIN `geo_city` ON 
            `geo_city`.`id`=`city_stocks`.`city_id` WHERE `city_stocks`.`stocks_id` =".(int)$data['id'];
            $city = $db->getAll($sql);
            $row['city'] = $city; 
            return $row;
        }

        public function all($data)
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `stocks` ORDER BY `id` DESC');
        }

        public function limm($data)
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
        }
    
    
    }
 