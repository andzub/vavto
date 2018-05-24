<?php
        
    $htmlblock_put = htmlblock_put::getInstance();
    class htmlblock_put {
        protected static $_instance;
        private function __clone() {}
        private function __wakeup() {}
        private function __construct() {}
    
        public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
        }

        public function edit_block($data){
	        $db = db::getInstance();
	        $arr=[];
	        $arr['val'] = (trim($data['val']));
	        $arr['name']=$data['block'];
	        $arr['page_name']=$data['page'];
	        $arr['lang']=$data['lang'];
	        if ( $db->getOne ( 'SELECT `name` FROM `htmlblock` WHERE `name`=?s AND `page_name`=?s AND `lang`=?s', $arr['name'],$arr['page_name'],$arr['lang'] ) ) {
		        $db->query('UPDATE `htmlblock` SET val=?s  WHERE `name`=?s AND `page_name`=?s AND `lang`=?s',$arr['val'],$arr['name'],$arr['page_name'],$arr['lang']);
	        }
	        else $db->query('INSERT INTO `htmlblock` SET ?u',$arr);
	        return true;
        }
        public function usual($data)
        {


        }
    
    
    }
        
    ?>