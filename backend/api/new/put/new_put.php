<?php
        
    $new_put = new_put::getInstance();
    class new_put {

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
		    $row = $db->getRow('SELECT * FROM `articles` WHERE `id`=?i',$id);
		    $current_lang=$data['cur_lang'];
		    if( !$row )
			    return array('error'=>true,'text'=>'Статья не существует.');
		    $title = htmlspecialchars(trim($data['title']));
		    $address = htmlspecialchars(trim($data['address']));
		    $short_desc = htmlspecialchars(trim($data['short_desc']));
		    $text = htmlspecialchars(trim($data['text']));

		    if ( ! $title ) return array ( 'error' => true , 'text' => 'Укажите заголовок статьи.' );
		    if ( ! $text ) return array ( 'error' => true , 'text' => 'Укажите текст статьи.' );
		    if ( ! $short_desc )return array ( 'error' => true , 'text' => 'Укажите описание статьи.' );


		    if($current_lang==='ru') {//ТОлько для основного языка
			    if( $data['is_franch'] ){
				    $is_franch = 1;
			    }else{
				    $is_franch = 0;
			    }
			    if ( ! $address ) return array ( 'error' => true , 'text' => 'Укажите адрес статьи.' );
			    if ( $db->getOne ( 'SELECT `id` FROM `articles` WHERE `address`=?s' , $address ) && $address != $row[ 'address' ] ) {
				    return array ( 'error' => true , 'text' => 'Статья с данным адресом уже существует.' );
			    }
			    $img = $files->upload ( $data[ 'img' ] , 'images' );
			    if ( $img[ 'status' ] == 'error' ) $img = $row[ 'img' ];
			    else  $img = $img[ 'url' ];
			    $img_min = $files->upload ( $data[ 'img_min' ] , 'images' );
			    if ( $img_min[ 'status' ] == 'error' ) $img_min = $row[ 'img_min' ];
			    else $img_min = $img_min[ 'url' ];

			    $array = array(
				    'address' => $address,
				    'img' => $img,
				    'img_min' => $img_min,
				    'type' => 2,
				    'is_franch' => $is_franch
			    );
			    $db->query('UPDATE `articles` SET ?u WHERE `id`=?i',$array,$id);
		    }
		    $art_lang_array = array(
			    'article_id' => $id,
			    'title' => $title,
			    'short_desc' => $short_desc,
			    'text' => $text,
			    'lang' => $current_lang,
		    );

		    if( $trans=$db->getOne('SELECT `id` FROM `articles_lang` WHERE article_id=?i AND lang=?s',$id,$current_lang) ){//Если такой перевод уже есть
			    $db->query('UPDATE `articles_lang` SET ?u WHERE `id`=?i',$art_lang_array,$trans);
		    }
		    else{
			    $db->query('INSERT INTO `articles_lang` SET ?u',$art_lang_array);
		    }
		    return array('status'=>true);

	    }
    
    
    }
        
    ?>