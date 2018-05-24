<?php
        
    $stocks_post = stocks_post::getInstance();
    class stocks_post {
    
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

            $title = htmlspecialchars(trim($data['title']));
            $description = trim($data['description']);
            $time = htmlspecialchars(trim($data['time']));


            $city = $data['city'];

            if(empty($city)) {
                return array('error'=>true,'text'=>'Укажите город.');
            }

            if(empty($time)) {
                return array('error'=>true,'text'=>'Укажите время до конца акции.');
            } else {
                $arrytime = explode('-', $time);
                $time = mktime(0, 0, 0, $arrytime[1], $arrytime[2], $arrytime[0]);
            }

            if(empty($description)) {
                return array('error'=>true,'text'=>'Укажите описание акции.');
            }

            if( $data['is_franch'] ){
                $is_franch = 1;
            }else{
                $is_franch = 0;
            }

            if( !$title )
                return array('error'=>true,'text'=>'Укажите заголовок акции.');


            $img = $files->upload($data['img'],'images');
            if( $img['status'] == 'error' ){
                return array('error'=>true,'text'=>'Ошибка при загрузки картинки: '.$img['text']);
            }

            $logo = $files->upload($data['logo'],'images');
            if( $logo['status'] == 'error' ){
                return array('error'=>true,'text'=>'Ошибка при загрузки логотипа: '.$img['text']);
            }

            $array = array(
                'title' => $title,
                'img' => $img['url'],
                'description' =>$description,
                'logo' => $logo['url'],
                'time' => $time,
            );
            $db->query('INSERT INTO `stocks` SET ?u',$array);
            $id_stocks = $db->insertId();

            // Добовляем город
            foreach ($city as $value) {
                $array2 = array(
                    'stocks_id' => $id_stocks,
                    'city_id' => $value
                );
                $db->query('INSERT INTO `city_stocks` SET ?u',$array2);
            }
            //


            return array('status'=>true);

        }
    
    
    }
        
    ?>