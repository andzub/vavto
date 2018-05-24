<?php
        
    $car_marks_put = car_marks_put::getInstance();
    class car_marks_put {
    
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
            $files = files::getInstance();

            $id = (int)$data['id'];
            $row = $db->getRow('SELECT * FROM `car_mark` WHERE `id`=?i',$id);

            if( !$row )
                return array('error'=>true,'text'=>'Марка не существует.');

            $name = htmlspecialchars(trim($data['name']));
	          $name_rus = htmlspecialchars(trim($data['name_rus']));


            if( !$name )
                return array('error'=>true,'text'=>'Укажите название (Англ)');
		        if( !$name_rus )
			        return array('error'=>true,'text'=>'Укажите название.');


            if( $db->getOne('SELECT `id` FROM `car_mark` WHERE `name`=?s',$name) && $name != $row['name'] )
                return array('error'=>true,'text'=>'Марка с данным названием уже существует.');
            $img = $files->upload($data['img'],'images');
            if( $img['status'] == 'error' ){
                $img = $row['img'];
            }else{
                $img = $img['url'];
            }

            $array = array(
                'name' => $name,
                'name_rus' => $name_rus,
                'img' => $img,
            );
            $db->query('UPDATE `car_mark` SET ?u WHERE `id`=?i',$array,$id);

            return array('status'=>true);

        }
    
    
    }
        
    ?>