<?php
        
    $cities_post = cities_post::getInstance();
    class cities_post {
    
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
            return $data;
        }

        public function districts($data)
        {

            $db = db::getInstance();
            $extra = extra::getInstance();

            $name = $data['name'];
            $city_id = (int)$data['city_id'];

            if(empty($name) || empty($city_id)) return $extra->notice('error', 401, 'Wrong data');

            $args = array(
                'city_id'=>$city_id,
                'name'=>$name
            );

            try{
                $db->query('INSERT INTO `geo_districts` SET ?u', $args);
            }catch (mysqli_sql_exception $e){
                return $extra->notice('error', 501, $e->getMessage());
            }catch (Error $e){
                return $extra->notice('error', 500, 'Internal error');
            }

            return $extra->notice('ok', 200);
        }
    
    
    }
        
    ?>