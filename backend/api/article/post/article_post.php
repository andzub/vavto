<?php
        
    $article_post = article_post::getInstance();
    class article_post {
    
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

            if( !empty($data['is_franch']) ){
                $is_franch = 1;
            }else{
                $is_franch = 0;
            }

            if( !$title )
                return array('error'=>true,'text'=>'Укажите заголовок статьи.');

            if( !$address )
                return array('error'=>true,'text'=>'Укажите адрес статьи.');

            if( $db->getOne('SELECT `id` FROM `articles` WHERE `address`=?s',$address) )
                return array('error'=>true,'text'=>'Статья с данным адресом уже существует.');

            if( !$short_desc )
                return array('error'=>true,'text'=>'Укажите описание статьи.');

            if( !$text )
                return array('error'=>true,'text'=>'Укажите текст статьи.');

            $img = $files->upload($data['img'],'images');
            if( $img['status'] == 'error' ){
                //return array('error'=>true,'text'=>'Ошибка при загрузки большой картинки: '.$img['text']);
                $img = '';
            }
            else $img=$img['url'];

            $img_min = $files->upload($data['img_min'],'images');
            if( $img_min['status'] == 'error' ){
                //return array('error'=>true,'text'=>'Ошибка при загрузки маленькой картинки: '.$img_min['text']);
                $img_min = '';
            }
            else $img_min=$img_min['url'];

            $art_array = array(
                'address' => $address,
                'img' => $img,
                'img_min' => $img_min,
                'is_franch' => $is_franch,
            );
            $db->query('INSERT INTO `articles` SET ?u',$art_array);
	        $art_id=$db->getOne('SELECT `id` FROM articles WHERE address=?s',$address);
	        $art_lang_array = array(
		        'article_id' => $art_id,
		        'title' => $title,
		        'short_desc' => $short_desc,
		        'text' => $text,
		        'lang' => 'ru',
	        );
	        $db->query('INSERT INTO `articles_lang` SET ?u',$art_lang_array);

            return array('status'=>true);

        }
    
    
    }
        
    ?>