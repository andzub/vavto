<?php
$news='';
if(count($url_parts)!==1) return false;
$url=$url_parts[0];
$page = $db->getRow('SELECT a.*,al.title,al.text,al.short_desc,al.lang 
					FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id 
					AND al.lang="'.$config['lang'].'") WHERE `address`=?s AND type=2',$url);
if($page){
	$page['text'] = htmlspecialchars_decode($page['text']);
	$page['prev'] = $db->getOne('SELECT address FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang="'.$config['lang'].'") WHERE a.`id`<'.$page['id'].' AND a.type=2 ORDER BY a.id DESC LIMIT 1');
	$page['next'] = $db->getOne('SELECT address FROM `articles` a INNER JOIN articles_lang al ON (a.id=al.article_id AND al.lang="'.$config['lang'].'") WHERE a.`id`>'.$page['id'].' AND a.type=2 ORDER BY a.id LIMIT 1');
	$page['date_create'] = date( 'd.m.Y - H:i', strtotime( $page['date_create'] ) );
	$template = 'new-page.html';
	$vars['row'] = $page;
}
else $template = '404.html';
?>
