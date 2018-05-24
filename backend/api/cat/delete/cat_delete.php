<?php
        
    $cat_delete = cat_delete::getInstance();
    class cat_delete {
    
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
            $db->query('DELETE FROM `cats` WHERE `id`=?i', (int)$data['id']);
	        $db->query('DELETE FROM `cats_lang` WHERE `cat_id`=?i', (int)$data['id']);
        }
    
    
    }
        
    ?>