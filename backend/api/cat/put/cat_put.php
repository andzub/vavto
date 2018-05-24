<?php
        
    $cat_put = cat_put::getInstance();
    class cat_put {
    
        protected static $_instance;
        private function __clone() {}
        private function __wakeup() {}
        private function __construct() {}
    
        public static function getInstance() {
            if (self::$_instance === null) { self::$_instance = new self; }
            return self::$_instance;
        }
        //сохранить категорию
        public function usual($data)
        {
            $db = db::getInstance();
            $files = files::getInstance();

            $id = (int)$data['id'];
            $row = $db->getRow('SELECT * FROM `cats` WHERE `id`=?i',$id);

            if( !$row ) return array('error'=>true,'text'=>'Объект не существует.');
			$current_lang=$data['cur_lang'];
            $title = htmlspecialchars(trim($data['title']));
            $short_desc = htmlspecialchars(trim($data['short_desc']));
            $text = htmlspecialchars(trim($data['text']));

            if($current_lang==='ru'){//ТОлько для основного языка
		        $address = htmlspecialchars(trim($data['address']));
		        $parent_id=(int)$data['parent_id'];
		        $type=(int)$data['type'];
	            if( !empty($data['is_franch']) ){
	                $is_franch = 1;
	            }else{
	                $is_franch = 0;
	            }

	            if( !$address )
	                return array('error'=>true,'text'=>'Укажите адрес статьи.');
	            if( $db->getOne('SELECT `id` FROM `cats` WHERE `address`=?s',$address) && $address != $row['address'] )
	                return array('error'=>true,'text'=>'Статья с данным адресом уже существует.');
		        $img = $files->upload($data['img'],'images');
		        if( $img['status'] == 'error' ){
			        $img = $row['img'];
		        }else{
			        $img = $img['url'];
		        }
		        $img_min = $files->upload($data['img_min'],'images');
		        if( $img_min['status'] == 'error' ){
			        $img_min = $row['img_min'];
		        }else{
			        $img_min = $img_min['url'];
		        }
	            $img_icon = $files->upload($data['img_icon'],'images');
	            if( $img_icon['status'] == 'error' ){
		            //$img_icon = '';
                    $img_icon = $row['img_icon'];

	            }else{
		            $img_icon = $img_icon['url'];
	            }
            }
	        if( !$title )
		        return array('error'=>true,'text'=>'Укажите заголовок статьи.');
            if( !$short_desc )
                return array('error'=>true,'text'=>'Укажите описание статьи.');

            if( !$text )
                return array('error'=>true,'text'=>'Укажите текст статьи.');


            if($current_lang==='ru') {
                $cats_array = array (
                    'address' => $address ,
                    'img' => $img ,
                    'img_min' => $img_min ,
                    'img_icon' => $img_icon,
                    'type' => $type ,
                    'parent_id' => $parent_id ,
                    'is_franch' => $is_franch
                );


		        $db->query ( 'UPDATE `cats` SET ?u WHERE `id`=?i' , $cats_array , $id );
	        }
	        $cats_lang_array = array(
	        	'cat_id' => $id,
		        'title' => $title,
		        'short_desc' => $short_desc,
		        'text' => $text,
		        'lang' => $current_lang,
	        );

	        if( $trans=$db->getOne('SELECT `id` FROM `cats_lang` WHERE cat_id=?i AND lang=?s',$id,$current_lang) ){//Если такой перевод уже есть
		        $db->query('UPDATE `cats_lang` SET ?u WHERE `id`=?i',$cats_lang_array,$trans);
	        }
	        else{
		        $db->query('INSERT INTO `cats_lang` SET ?u',$cats_lang_array);
	        }
            return array('status'=>true);

        }
    
    
    }
        
    ?>