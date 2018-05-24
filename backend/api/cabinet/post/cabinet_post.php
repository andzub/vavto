<?php

    //$stocks_post = cabinet_post::getInstance();
    $cabinet_post = cabinet_post::getInstance();
    class cabinet_post {
    
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
            global $config;
            $db = db::getInstance();

            $name = htmlspecialchars(trim($data['name']));
            $surname = htmlspecialchars(trim($data['surname']));
            $pass = htmlspecialchars(trim($data['pass']));
            $pass_p = htmlspecialchars(trim($data['pass_p']));
            $email = htmlspecialchars(trim($data['email']));

            if (!preg_match("/^[a-zA-Z0-9_\-.]+@[a-z]/", $email)) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'The format of the email input is not correct!');
                else return array('error'=>true,'text'=>'Формат ввода email не верен!');
            }

            if (empty($name)) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'Please enter a username!');
                else return array('error'=>true,'text'=>'Укажите имя пользователя!');
            } 

            if (empty($surname)) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'Enter the last name of the user!');
                else return array('error'=>true,'text'=>'Укажите фамилию пользователя!');
            }

            if (empty($pass)) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'Enter the user password!');
                else return array('error'=>true,'text'=>'Укажите пароль пользователя!');
            } else {
                $password = md5($pass . "avtoservis");
            }

            if ($pass != $pass_p) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'Passwords do not match!');
                else return array('error'=>true,'text'=>'Пароли не совпадают!');
            }


            $row = $db->getRow('SELECT * FROM `client` WHERE `client`.`email`=?s',$email);

                   
            if (isset($row['id']))
            {
                 if($config['lang']=='en') return array('error'=>true,'text'=>'Email already exists !');
                 else return array('error'=>true,'text'=>'Такой Email уже существует !');
            }    


            $array = array(
                'name' => $name,
                'famaliname' => $surname,
                'password' => $password,
                'email' => $email,
            );
            $db->query('INSERT INTO `client` SET ?u',$array);
            $id = $db->insertId();
            
            return array('status'=>true,'email'=>$email,'id'=>$id);

        } 
        public function car_add($data)
        {
            global $config;
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
                 if($config['lang']=='en') return array('error'=>true,'text'=>'Choose a brand of car !');
                 else return array('error'=>true,'text'=>'Выберите марку автомобиля !');
            } 

            if (empty($model)) {
                if($config['lang']=='en') return array('error'=>true,'text'=>'Choose a car model !');
                else return array('error'=>true,'text'=>'Выберите модель автомобиля !');
            } 


            $array = array(
                'client_id' => $_SESSION['client']['id'],
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

            $db->query('INSERT INTO `cars_clients` SET ?u',$array);
            //$id = $db->insertId();
            
            return array('status'=>true,);


        }
    }
