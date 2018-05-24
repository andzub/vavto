<?php
    $cabinet_put = cabinet_put::getInstance();
    class cabinet_put {
    
        protected static $_instance;
        private function __clone() {}
        private function __wakeup() {}
        private function __construct() {}
    
        public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
        }

        public function pass_set($data){
            $db = db::getInstance();
            $pass = htmlspecialchars(trim($data['pass']));
            $pass_p = htmlspecialchars(trim($data['pass_p']));

            if(empty($pass)) {
                return array('error'=>true,'text'=>'Заполните поле "Новый пароь"');
            }
            if(empty($pass_p)) {
                return array('error'=>true,'text'=>'Заполните поле "Повторите пароль" !');
            }

            if($pass != $pass_p) {
                return array('error'=>true,'text'=>'Пароли не совподают !');
            }

            $password = md5($pass . "avtoservis");

            $array = array(
                'password' => $password,
            );
            $db->query('UPDATE `client` SET ?u WHERE `email`=?s',$array,$data['email']);
            return array('status'=>true,'text'=>'Пароль был успешно установлен!');

        }

        public function pass_reset($data)
        {
            
            $db = db::getInstance();
            $pass = htmlspecialchars(trim($data['pass']));
            $newpass = htmlspecialchars(trim($data['newpass']));
            $newpass_p = htmlspecialchars(trim($data['newpass_p']));

            if(empty($pass)) {
                return array('error'=>true,'text'=>'Заполните поле "Ваш пароль"');
            }
            if(empty($newpass)) {
                return array('error'=>true,'text'=>'Заполните поле "Новый пароь"');
            }
            if(empty($newpass_p)) {
                return array('error'=>true,'text'=>'Заполните поле "Повторите пароль" !');
            }

            $password = md5($pass . "avtoservis");

            if($_SESSION['client']['password'] != $password) {
                return array('error'=>true,'text'=>'Пароль неверен !');
            }

            if($newpass != $newpass_p) {
                return array('error'=>true,'text'=>'Пароли не совподают !');
            }

            $newpass = md5($newpass . "avtoservis");

            $array = array(
                'password' => $newpass,
            );
            $db->query('UPDATE `client` SET ?u WHERE `id`=?i',$array,$_SESSION['client']['id']);

            return array('status'=>true);

        }
    
        public function status($id)
        {
            $db = db::getInstance();
            $respons = $db->getdb()->query('
                UPDATE `client` SET `status`= 1,`purse` = 500 
                WHERE `id`='.$id['id']);
            return $respons;
        }

        public function data($data)
        {
            $db = db::getInstance();

            $surname = htmlspecialchars(trim($data['surname']));
            $name = htmlspecialchars(trim($data['name']));
            $city = (int)$data['city'];
            $phone = htmlspecialchars(trim($data['phone']));
            $patronymic = htmlspecialchars(trim($data['patronymic']));
            $email = htmlspecialchars(trim($data['email']));
            $date = htmlspecialchars(trim($data['date']));

            if(empty($name)) {
                return array('error'=>true,'text'=>'Укажите имя.');
            }
            if(empty($email)) {
                return array('error'=>true,'text'=>'Укажите email.');
            }
            if(empty($surname)) {
                return array('error'=>true,'text'=>'Укажите фамилию.');
            }
            if(empty($city)) {
                return array('error'=>true,'text'=>'Укажите город.');
            }
               
            $array = array(
                'name' => $name,
                'email' => $email,
                'tel' => $phone,
                'patronymic'=> $patronymic,
                'famaliname' => $surname,
                'date' => $date,
                'city_id'=> $city,
            );
            $db->query('UPDATE `client` SET ?u WHERE `id`=?i',$array,$_SESSION['client']['id']);

            return array('status'=>true);
        }

        public function car_update($data)
        {
            $db = db::getInstance();

            $marka = (int)$data['marka'];
            $model = (int)$data['model'];
            $year_issue = (int)$data['year_issue'];
            
            $vin = htmlspecialchars(trim($data['vin']));
            $number_gos = htmlspecialchars(trim($data['number_gos']));
            $tires_summer = htmlspecialchars(trim($data['tires_summer']));
            $tires_winter = htmlspecialchars(trim($data['tires_winter']));
            $osago_do = htmlspecialchars(trim($data['osago_do']));
            $osago_strah = htmlspecialchars(trim($data['osago_strah']));
            $kasko_do = htmlspecialchars(trim($data['kasko_do']));
            $kasko_strah = htmlspecialchars(trim($data['kasko_strah']));


            if (empty($marka)) {
                return array('error'=>true,'text'=>'Выберите марку автомобиля !');
            } 

            if (empty($model)) {
                return array('error'=>true,'text'=>'Выберите модель автомобиля !');
            } 

            $array = array(
                'autoBrand_id' => $marka,
                'autoModel_id' => $model,
                'year_issue' => $year_issue,
                'vin' => $vin,
                'number_gos' => $number_gos,
                'tires_summer' => $tires_summer,
                'tires_winter' => $tires_winter,
                'osago_do' => $osago_do,
                'osago_strah' => $osago_strah,
                'kasko_do' => $kasko_do,
                'kasko_strah' => $kasko_strah,
            );

            $db->query('UPDATE `cars_clients` SET ?u WHERE `id`=?i',$array,$data['id']);
            return array('status'=>true);
        }
        
        
        //сохранить заявку менеджером
        public function saveorder($data)
        {
        
     /*   echo "<pre>";
        var_dump($data);
        echo "</pre>";
        exit;*/
            $db = db::getInstance();

//1colum
            $id_order= (int)$data['id'];
            $status = (int)$data['status'];
            $date_udobno = htmlspecialchars(trim($data['date_udobno']));
            $time_udobno = htmlspecialchars(trim($data['time']));
 
            $date_udobno=$date_udobno." ".$time_udobno.":00";
            $date_udobno = strtotime($time_udobno);
            //echo $date_udobno;
            //return
            //$date = '2016-05-24 16:32:45';
            return array('error'=>true, 'text'=>"".$date_udobno);
            
            exit;
            $real_date_udobno = $date_udobno - $time_udobno; //собираем в юникс
            $category = (int)$data['category'];
            $work_type=(int)$data["work_type_".$category];
            if($work_type) $category=$work_type;
            
            $service = htmlspecialchars(trim($data['service']));
            $problem = htmlspecialchars(trim($data['problem']));

//2colum
            $cl_name = htmlspecialchars(trim($data['cl_name']));
            $cl_email = htmlspecialchars(trim($data['cl_email']));
            $cl_tel = htmlspecialchars(trim($data['cl_tel']));
            $city = htmlspecialchars(trim($data['city']));
            $cl_rayon = htmlspecialchars(trim($data['cl_rayon']));

//3colum
            $car_marka = (int)$data['marka'];
            $car_model = (int)$data['model'];
            $car_vin = htmlspecialchars(trim($data['car_vin']));
            $car_year = htmlspecialchars(trim($data['car_year']));
            


            if (empty($car_marka)) {
                return array('error'=>true,'text'=>'Выберите марку автомобиля !');
            }

            if (empty($car_model)) {
                return array('error'=>true,'text'=>'Выберите модель автомобиля !');
            }

            if (empty($cl_name)) {
                return array('error'=>true,'text'=>'Имя обязательно для заполнения !');
            }

            if (empty($cl_tel)) {
                return array('error'=>true,'text'=>'Номер телефона обязателен для заполнения  !');
            }
            

            $array = array(
                'cl_name' => $cl_name,
                'cl_email' => $cl_email,
                'cl_tel' => $cl_tel,
                'cl_city' => $city,
                'cl_rayon' => $cl_rayon,
                
                'car_marka' => $car_marka,
                'car_model' => $car_model,
                'car_vin' => $car_vin,
                'car_year' => $car_year,
                
                'status' => $status,
                'date_udobno' => $date_udobno,
                'category' => $category,
                'service' => $service,
                'problem' => $problem,
            );
            
            





            $db->query('UPDATE `history` SET ?u WHERE `id`=?i',$array,$id_order);
            return array('status'=>true);
        }
        
    }
