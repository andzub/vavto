<?php
        
    $car_marks_delete = car_marks_delete::getInstance();
    class car_marks_delete {
    
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
            $db->query('DELETE FROM `car_mark` WHERE `id`=?i', (int)$data['id']);
        }

    }
?>

