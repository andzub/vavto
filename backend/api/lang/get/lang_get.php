<?php
        
    $lang_get = lang_get::getInstance();
    class lang_get {
    
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

        public function all($data=[])
        {
            $db = db::getInstance();
	        return $db->getAll('SELECT * FROM `lang`');
        }
        
    }
        
    ?>