<?php

    $new_delete = new_delete::getInstance();
    class new_delete {
    
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
            $db->query('DELETE FROM `articles` WHERE `id`=?i', (int)$data['id']);
	        $db->query('DELETE FROM `articles_lang` WHERE `article_id`=?i', (int)$data['id']);
        }
    
    
    }
        
    ?>