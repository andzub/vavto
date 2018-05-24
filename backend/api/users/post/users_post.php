<?php
        
    $users_post = users_post::getInstance();
    class users_post {
    
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


            //валидируем
            $city_id = (int)$data['city_id'];
            $purse = (int)$data['purse'];
            $role = (int)$data['role'];
            $status = $data['status'];

            $famaliname = htmlspecialchars(trim($data['famaliname']));
            $name = htmlspecialchars(trim($data['name']));
            $patronymic = htmlspecialchars(trim($data['patronymic']));
            $email = (trim($data['email']));
            $tel = (trim($data['tel']));
            
            $password = (trim($data['password']));

            //сообщения. не все поля заполнены
            if( !$name )
                return array('error'=>true,'text'=>'Укажите имя пользователя.');
            if( !$famaliname )
                return array('error'=>true,'text'=>'Укажите фамилию пользователя.');
            if( !$email )
                return array('error'=>true,'text'=>'Укажите Email пользователя.');
            if( !$tel )
                return array('error'=>true,'text'=>"Укажите -$tel- номер телефона пользователя.");

            $date=date("Y-m-d",time()); //дата добавления

            //сочиняем пароль
            $password = md5($password."avtoservis");

            //данные
            $array = array(
                 'date' => $date,
                 'city_id' => $city_id,
                 'purse' => $purse,
                 'role' => $role,
                 'status'=> $status,
                 'famaliname' => $famaliname,
                 'name' => $name,
                 'patronymic' => $patronymic,
                 'email' => $email,
                 'password' => $password,
                 'tel' => $tel
            );

            //пишем в бд
            $db->query('INSERT INTO `client` SET ?u',$array);
            $id_stocks = $db->insertId();

       
            return array('status'=>true);

        }
    
    
    }
        
    ?>