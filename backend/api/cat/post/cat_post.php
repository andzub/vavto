<?php
        
    $cat_post = cat_post::getInstance();
    class cat_post {
    
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
            $address = htmlspecialchars(trim($data['address']));
            $short_desc = htmlspecialchars(trim($data['short_desc']));
            $text = htmlspecialchars(trim($data['text']));
			$choice = htmlspecialchars(trim($data['text']));
	        $parent_id=(int)$data['parent_id'];
	        $type=(int)$data['type'];
            if( $data['is_franch'] ){
                $is_franch = 1;
            }else{
                $is_franch = 0;
            }

			
			
            if( !$title )
                return array('error'=>true,'text'=>'Укажите заголовок статьи.');

            if( !$address )
                return array('error'=>true,'text'=>'Укажите адрес статьи.');

            if( $db->getOne('SELECT `id` FROM `cats` WHERE `address`=?s',$address) )
                return array('error'=>true,'text'=>'Статья с данным адресом уже существует.');

            if( !$short_desc )
                return array('error'=>true,'text'=>'Укажите описание статьи.');

            if( !$text )
                return array('error'=>true,'text'=>'Укажите текст статьи.');


            $img = $files->upload($data['img'],'images');
            if( $img['status'] == 'error' ){
                $img = '';
            }

            $img_min = $files->upload($data['img_min'],'images');
            if( $img_min['status'] == 'error' ){
                $img_min = '';
            }
	        $img_icon = $files->upload($data['img_icon'],'images');
	        if( $img_min['status'] == 'error' ){
		        $img_icon = '';
	        }


	        $array = array(
            	'address' => $address,
                'img' => $img['url'],
                'img_min' => $img_min['url'],
		        'img_icon' => $img_icon['url'],
	            'type' => $type,
	            'parent_id' => $parent_id,
                'is_franch' => $is_franch
            );
            $cat=$db->query('INSERT INTO `cats` SET ?u',$array);
			if(!$cat) return ['status'=>false];
			$cat_id=$db->getOne('SELECT `id` FROM cats WHERE address=?s',$address);
	        $cats_lang_array = array(
		        'cat_id' => $cat_id,
		        'title' => $title,
		        'short_desc' => $short_desc,
		        'text' => $text,
		        'lang' => 'ru',
	        );
	        $db->query('INSERT INTO `cats_lang` SET ?u',$cats_lang_array);

            return array('status'=>true);

        }
    
    
    }
        
    ?>