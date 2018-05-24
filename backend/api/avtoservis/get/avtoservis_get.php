<?php
    $avtoservis_get = avtoservis_get::getInstance();
    class avtoservis_get {
    
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
            $sql = 'SELECT * FROM `avtoservis`';
            $avtoservis = $db->getRow($sql);
            $sql = 'SELECT * FROM `geo_city`  WHERE `id` = ?i';
            $city = $db->getRow($sql,$avtoservis['city_id']);
            $avtoservis['city'] = $city['name'];
            return $avtoservis;
        }

    }
