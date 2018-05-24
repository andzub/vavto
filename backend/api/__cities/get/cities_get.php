<?php
        
    $cities_get = cities_get::getInstance();
    class cities_get {
    
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

            $region_id = (int)$data[0];
            $page = (int)$data[1] ?: 1;
            $limit = 20;

            $start = $limit * (int)$page - $limit;

            if ((int)$region_id > 0) {
                $pages = ceil((int)$db->getOne('SELECT COUNT(*) FROM `geo_city` WHERE `region_id` = ?i', (int)$region_id) / $limit);
                $city = $db->getAll('SELECT * FROM `geo_city` WHERE `region_id` = ?i ORDER BY `name` ASC LIMIT ?i, ?i', (int)$region_id, $start, $limit);
            } else {
                $pages = ceil((int)$db->getOne('SELECT COUNT(*) FROM `geo_city`') / $limit);
                $city = $db->getAll('SELECT * FROM `geo_city` ORDER BY `name` ASC LIMIT ?i, ?i', $start, $limit);
            }
            return array(
                'city' => $city,
                'pages' => $pages
            );
        }

        public function lang($data){
	        $db = db::getInstance();
	        $lang = $data['lang'];
	        $page = (int)$data['page'] ?: 1;
	        $limit = 500;
	        $start = $limit * (int)$page - $limit;
	        $like='';
	        $pages = ceil((int)$db->getOne('SELECT COUNT(*) FROM `geo_city`') / $limit);
	        if(!empty($_POST['filter']['name'])) $like=' WHERE c.name LIKE \'%'.$_POST['filter']['name'].'%\'';
	        $rows=$db->getAll('SELECT c.id,c.name,cl.title FROM geo_city c LEFT JOIN geo_city_lang cl ON (c.id = cl.city_id AND cl.lang=?s)'.$like.' ORDER BY cl.lang,c.name LIMIT ?i, ?i',$lang,$start,$limit);
	        return['pgn_count'=>$pages,'rows'=>$rows];
         }
        public function regions()
        {
            $db = db::getInstance();
            return $db->getAll('SELECT * FROM `geo_regions` ORDER BY `name` ASC');
        }

        public function city($data)
        {
            $db = db::getInstance();

            $city = $db->getRow('SELECT * FROM `geo_city` WHERE `id` = ?i', (int)$data[0]);
            $districts = $db->getAll('SELECT * FROM `geo_districts` WHERE `city_id` = ?i', (int)$data[0]);

            return array(
                'city' => $city,
                'districts' => $districts
            );
            
        }
    
    }
        
    ?>