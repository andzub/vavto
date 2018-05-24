<?php
        
    $new_get = new_get::getInstance();
    class new_get {
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
		    $data['lang']=isset($data['lang']) ? $data['lang'] : 'ru';

		    $db = db::getInstance();
		    return $db->getRow('SELECT a.id,a.address,al.title,al.text,al.short_desc,al.lang,a.img,a.img_min,a.type FROM `articles` a LEFT JOIN articles_lang al ON (a.id=al.article_id AND al.lang="'.$data['lang'].'") WHERE a.`id`=?i', (int)$data['id']);
	    }

	    public function all($data)
	    {
		    $db = db::getInstance();
		    return $db->getAll('SELECT a.*,al.title,al.short_desc,al.lang FROM `articles` a LEFT JOIN articles_lang al ON (a.id=al.article_id AND al.lang="ru") WHERE a.`type`=2');
	    }

	    public function franch($data)
	    {
		    $db = db::getInstance();
		    return $db->getAll('SELECT * FROM `articles` WHERE `is_franch`=1 AND type=1 ORDER BY `id` DESC');
	    }



    }
        
    ?>