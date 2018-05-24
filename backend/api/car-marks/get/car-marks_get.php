<?php
        
    $article_get = article_get::getInstance();
    class car-marks_get {
    
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
            return $db->getRow('SELECT * FROM `car_mark` WHERE `id`=?i', (int)$data['id']);
        }

        public function all($data)
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `car_mark` ORDER BY `name_rus` ASC');
        }

        public function franch($data)
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `car_mark` WHERE `is_franch`=1 ORDER BY `id` DESC');
        }
    
    
    }
        
    ?>