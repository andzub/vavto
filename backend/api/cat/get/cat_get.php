<?php
        
    $cat_get = cat_get::getInstance();
    class cat_get {
    
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
	        $data['lang']=isset($data['lang']) ? $data['lang'] : 'ru';
            $db = db::getInstance();
            return $db->getRow('SELECT c.id,c.address,cl.title,cl.text,cl.short_desc,cl.lang,c.img,c.img_min,c.type,c.parent_id,c.img_icon FROM `cats` c LEFT JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang="'.$data['lang'].'") WHERE c.`id`=?i', (int)$data['id']);
        }

        public function all($data)
        {
	        $data['lang']=isset($data['lang']) ? $data['lang'] : 'ru';
            $db = db::getInstance();
            return $db->getAll('SELECT c.id,c.address,cl.title,cl.short_desc,cl.lang,c.img,c.img_min,c.type,c.parent_id,c.img_icon,p.address parent_address FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang="'.$data['lang'].'")LEFT JOIN cats p ON c.parent_id=p.id ORDER BY c.`id` DESC');
        }
         public function allsort($data){
	         $data['lang']=isset($data['lang']) ? $data['lang'] : 'ru';
	         $db = db::getInstance();
	         return $db->getAll('SELECT c.id,c.address,cl.title,cl.short_desc,cl.lang,c.img,c.img_min,c.type,c.parent_id,c.img_icon FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang="'.$data['lang'].'") ORDER BY c.`type`,cl.title');
         }

        public function franch($data)
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `cats` WHERE `is_franch`=1 ORDER BY `id` DESC');
        }
    
    
    }
        
    ?>