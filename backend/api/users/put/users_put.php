<?php
        
    $users_put = users_put::getInstance();
    class users_put {
    
        protected static $_instance;
        private function __clone() {}
        private function __wakeup() {}
        private function __construct() {}
    
        public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
        }

        public function usual($data) // сохранить юзера
        {
            $db = db::getInstance();

            //валидируем
            $id = (int)$data['id'];
            $city_id = (int)$data['city_id'];
            $purse = (int)$data['purse'];
            $role = (int)$data['role'];
            $status = $data['status'];

            $famaliname = htmlspecialchars(trim($data['famaliname']));
            $name = htmlspecialchars(trim($data['name']));
            $patronymic = htmlspecialchars(trim($data['patronymic']));
            $email = (trim($data['email']));
            $tel = (trim($data['tel']));

            //сообщения. не все поля заполнены
            if( !$name )
                return array('error'=>true,'text'=>'Укажите имя пользователя.');
            if( !$famaliname )
                return array('error'=>true,'text'=>'Укажите фамилию пользователя.');
            if( !$email )
                return array('error'=>true,'text'=>'Укажите Email пользователя.');
            if( !$tel )
                return array('error'=>true,'text'=>"Укажите -$tel- номер телефона пользователя.");



            //данные
            $array = array(
                 'city_id' => $city_id,
                 'purse' => $purse,
                 'role' => $role,
                 'status'=> $status,
                 'famaliname' => $famaliname,
                 'name' => $name,
                 'patronymic' => $patronymic,
                 'email' => $email,
                 'tel' => $tel
            );

            //пишем в бд
            $db->query('UPDATE `client` SET ?u WHERE `id`=?i',$array,$id);

            //результат все ок!
            return array('status'=>true);


        }
    
    
    }
        
    ?>