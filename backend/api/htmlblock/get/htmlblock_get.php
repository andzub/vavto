<?php
        
    $htmlblock_get = htmlblock_get::getInstance();
    class htmlblock_get {
    
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
		    return $db->getAll('SELECT * FROM  `htmlblock` WHERE page_name=?s AND name=?s',$data['page_name'],$data['name']);
	    }

        public function page_names($data=[])
        {
            $db = db::getInstance();
	        return $db->getAll('SELECT DISTINCT page_name FROM `htmlblock`');
        }

	    public function page_blocks($data)
	    {
		    $db = db::getInstance();
		    return $db->getAll('SELECT * FROM `htmlblock` WHERE page_name=?s ORDER BY name',$data['page_name']);
	    }
    }
        
    ?>