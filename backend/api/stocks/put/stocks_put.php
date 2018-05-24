<?php
        
    $stocks_put = stocks_put::getInstance();
    class stocks_put {
    
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


            $description = $data['description'];
            $time = htmlspecialchars(trim($data['time']));
            $id = (int)$data['id'];
            $title = htmlspecialchars(trim($data['title']));


            $row = $db->getRow('SELECT * FROM `stocks` WHERE `id`=?i',$id);

            if( !$row )
                return array('error'=>true,'text'=>'Акция не существует.');

            

            if( !$title )
                return array('error'=>true,'text'=>'Укажите заголовок акции.');

            $city = $data['city'];

            if(empty($time)) {
                return array('error'=>true,'text'=>'Укажите время до конца акции.');
            } else {
                $arrytime = explode('-', $time);
                $time = mktime(0, 0, 0, $arrytime[1], $arrytime[2], $arrytime[0]);
            }


            $img = $files->upload($data['img'],'images');
            if( $img['status'] == 'error' ){
                $img = $row['img'];
            }else{
                $img = $img['url'];
            }

            $logo = $files->upload($data['logo'],'images');
            if( $logo['status'] == 'error' ){
                $logo = $row['logo'];
            } else {
                $logo = $logo['url'];
            }


            $array = array(
                'title' => $title,
                'img' => $img,
                'description' => $description,
                'time'=> $time,
                'logo' => $logo
            );
            $db->query('UPDATE `stocks` SET ?u WHERE `id`=?i',$array,$id);

            if (!empty($city)) {
                foreach ($city as $value) {
                    $array2 = array(
                        'stocks_id' => $id,
                        'city_id' => $value
                    );
                    $sql = 'SELECT * FROM `city_stocks`
                     WHERE `city_id` = '.$value.' AND `stocks_id` ='.$id;//
                    $rez = $db->getdb()->query($sql);
                    $row = mysqli_num_rows($rez);
                   
                    if (!($row > 0))
                    {
                        $db->query('INSERT INTO `city_stocks` SET ?u',$array2);
                    }
                }
            }
            return array('status'=>true);

        }
    
    
    }
        
    ?>