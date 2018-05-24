<?php
        
    $users_delete = users_delete::getInstance();
    class users_delete {
    
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
            $db->query('DELETE FROM `cars_clients` WHERE `client_id`=?i', (int)$data['id']);//удалить юзерские авто
            $db->query('DELETE FROM `client` WHERE `id`=?i', (int)$data['id']); //удалить юзера
        }
    
    
    }
        
    ?>