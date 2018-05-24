<?php
        
    $cities_put = cities_put::getInstance();
    class cities_put {
    
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
            $extra = extra::getInstance();


            $id = (int)$data['id'];
            $name = htmlspecialchars(trim($data['name']));
            $phone = htmlspecialchars(trim($data['phone']));
            $yaCounter = $data['yaCounter'];
            $code_metrika = $data['code_metrika'];
            $city_email = $data['city_email'];
            $code_footer = $data['code_footer'];

            if(empty($id) || empty($name)) return $extra->notice('error', 401, 'Wrong data');

            $args = array(
                'phone' => $phone,
                'yaCounter' => $yaCounter,
                'code_metrika' => $code_metrika,
                'city_email' => $city_email,
                'code_footer' => $code_footer,
                'name' => $name
            );

            try{
                $db->query('UPDATE `geo_city` SET ?u WHERE `id`=?i', $args, $id);
            }catch (mysqli_sql_exception $e){
                return $extra->notice('error', 501, $e->getMessage());
            }catch (Error $e){
                return $extra->notice('error', 500, 'Internal error');
            }

            return $extra->notice('ok', 200);
        }

        public function districts($data)
        {
            $db = db::getInstance();
            $extra = extra::getInstance();

            $name = $data['name'];
            $id = (int)$data['id'];
            $city_id = (int)$data['city_id'];

            if(empty($name) || empty($city_id) || empty($id)) return $extra->notice('error', 401, 'Wrong data');

            $args = array(
                'city_id'=>$city_id,
                'name'=>$name
            );

            try{
                $db->query('UPDATE `geo_districts` SET ?u WHERE `id`=?i', $args, $id);
            }catch (mysqli_sql_exception $e){
                return $extra->notice('error', 501, $e->getMessage());
            }catch (Error $e){
                return $extra->notice('error', 500, 'Internal error');
            }

            return $extra->notice('ok', 200);
        }    
    }
        
    ?>