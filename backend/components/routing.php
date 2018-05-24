<?php

$routing = routing::getInstance();

class routing
{

    private $data;

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    //Обработчик , принимает полный URL + разбитый на части без GET
    public function init($url_parts)
    {
        global $config;
        $db = db::getInstance();
        //Убираем слеш вначале и в конце
        //$url = explode('/',$url);
        $url = $url_parts[0];
        if ($url === 'car-marks') {//Раздел марок
            return array(
                'controller' => 'backend/controllers/general/car-marks-list.php',
                'template' => 'general/car-marks-list.html'
            );
        } elseif ($url === 'new') {//Страница новости
            return array( 'controller' => 'backend/controllers/news/new-page.php' );
        }

        $count_parts=count ($url_parts);
        if ($count_parts === 1) {
            $page = $db->getRow('SELECT a.*,al.title,al.text,al.short_desc,al.lang FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang=?s) WHERE a.`address`=?s AND a.type=1',$config['lang'],$url);
            if ( $page ) {
                $page['text'] = htmlspecialchars_decode($page['text']);
                $text = explode('{form_application}',$page['text']);
                $page['text'] = $text;
                $page['similar'] = $db->getAll('SELECT  a.*,al.title,al.short_desc,al.lang FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang=?s) WHERE a.`id`!=?i AND a.type=1 ORDER BY rand() LIMIT 8',$config['lang'],$page['id']);
                $this->data = $page;
                return array(
                    'controller' => 'backend/controllers/general/info-material.php',
                    'template' => 'general/info-material.html'
                );
            }
        }

        if ($count_parts < 3) {
            $page = $db->getRow('SELECT c.*,cl.title,cl.short_desc,cl.text 
                                FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s) 
                                WHERE c.`address`=?s AND c.parent_id=0', $config['lang'], $url);
            if ($page) {

                //Если вложенная категория/вид работ
                if ($count_parts === 2) {
                    $parent=$page;
                    
                    $lang1 = $config['lang'];
                    $parent_id = (int)$parent['id'];

                    $sql = 'SELECT c.*,cl.title,cl.short_desc,cl.text FROM cats c INNER JOIN cats_lang cl 
                        ON (c.id=cl.cat_id AND cl.lang="'.$lang1.'" ) WHERE `address`="'. $url_parts[1] .'" 
                        AND c.parent_id='.$parent_id;

                    $page = $db->getRow($sql);
                    
                    if (!$page) return false;

                    $page['parent'] = $parent;
                }

                $this->data = $page;
                return ['controller' => 'backend/controllers/general/cat.php'];
            }
        }
        return false;
    }

    //Получить данные с роутинга
    public function getData()
    {
        return $this->data;
    }

}