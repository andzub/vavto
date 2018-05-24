<?php
        
    $cities_delete = cities_delete::getInstance();
    class cities_delete {
    
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

            $id = (int)$data['id'];

            if(empty($id)) return $extra->notice('error', 401, 'Wrong data');

            try{
                $db->query('DELETE FROM `geo_districts` WHERE `id`=?i', $id);
            }catch (mysqli_sql_exception $e){
                return $extra->notice('error', 501, $e->getMessage());
            }catch (Error $e){
                return $extra->notice('error', 500, 'Internal error');
            }

            return $extra->notice('ok', 200);
        }
    
    
    }
        
    ?>