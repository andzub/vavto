<?php
$max_on_page=12;
$offset='';
if(!empty($_GET['page'])) $offset=' OFFSET '.((int)$_GET['page']-1)*$max_on_page;
$elem_count = $db->getOne('SELECT COUNT(*) count FROM `articles` WHERE type=1');
$elem_count = ($elem_count) ? $elem_count : 0;
$elems=$db->getAll('SELECT * FROM `articles` WHERE type=1 ORDER BY id DESC LIMIT '.$max_on_page.$offset);
foreach ($elems as &$article){
	$article['date_create'] = date( 'd.m.Y - H:i', strtotime( $article['date_create'] ) );
	$article['link']="/{$article['address']}/";
}

$template = 'blog.html';
$pagination=$extra->pagination(['total_count'=>$elem_count,'notes_in_page'=>$max_on_page]);
$vars = array('posts' => $elems,'pagination' => $pagination);
?>
