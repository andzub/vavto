<?php
        
    $htmlblock_delete = htmlblock_delete::getInstance();
    class htmlblock_delete {
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
            $db->query('DELETE FROM `htmlblock` WHERE `name`=?s AND page_name=?s', $data['name'],$data['page_name']);
        }
    }
    ?>