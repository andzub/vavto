<?php
        
    $lang_delete = lang_delete::getInstance();
    class lang_delete {
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
        }
    }
    ?>