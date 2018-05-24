<?php
$max_on_page=12;
$offset='';
if(!empty($_GET['page'])) $offset=' OFFSET '.((int)$_GET['page']-1)*$max_on_page;
$news_count = $db->getOne('SELECT COUNT(*) count FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang="'.$config['lang'].'") WHERE type=2');
$news_count = ($news_count) ? $news_count : 0;
$news=$db->getAll('SELECT a.id,a.address,al.title,al.short_desc,al.lang,a.img,a.img_min,a.type FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang="'.$config['lang'].'") WHERE a.type=2 ORDER BY a.id DESC LIMIT '.$max_on_page.$offset);

foreach ($news as &$new){
	$new['date_create'] = date( 'd.m.Y - H:i', strtotime( $new['date_create'] ) );
	$new['link']="new/{$new['address']}/";
}

$template = 'news.html';
$pagination=$extra->pagination(['total_count'=>$news_count,'notes_in_page'=>$max_on_page]);
$vars['posts'] = $news;
$vars['pagination'] = $pagination;
?>
