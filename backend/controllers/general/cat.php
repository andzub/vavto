<?php

//putenv('LC_ALL=ru_RU');
//setlocale(LC_ALL, 'ru_RU');
//putenv('LC_ALL=en_EN');
//setlocale(LC_ALL, '');
//putenv("LANGUAGE=en_EN");
$page=$routing->getData();
$forms_list=$settings->formList();
$template = 'general/cat.html';
$text = htmlspecialchars_decode($page['text']);
$page['form']='';
if(preg_match('/\{form_[\w]{1,20}?\}/ui',$text,$matches,PREG_OFFSET_CAPTURE)){
	$form=trim($matches[0][0],'{}');
	if(isset($forms_list[$form])) $page['form']=$forms_list[$form];
	$page['text']=explode($matches[0][0],$text);
}
else $page['text']=[$text];
$page['similar'] = $db->getAll('SELECT c.*,cl.title,cl.short_desc FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s) WHERE c.`id`!=?i AND c.img_min !="" AND c.parent_id=0 ORDER BY rand() LIMIT 6',$config['lang'],$page['id']);
if($page['type']==1){
	$page['childs_works']=$db->getAll('SELECT c.*,cl.title,cl.short_desc FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s) WHERE c.type=2 AND c.parent_id='.$page['id'],$config['lang']);
	$vars['reviews']=$settings->get_block_val(['page'=>'index','lang'=>$config['lang']]);
}
$workCategory=[];
$workType=[];
$cat_works=$db->getAll('SELECT c.*,cl.title,cl.short_desc FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s)',$config['lang']);
foreach ($cat_works as $elem){
	if($elem['type']==1) $workCategory[$elem['id']]=$elem['title'];
	elseif($elem['type']==2) {
		if(isset($workType[$elem['parent_id']])){
			$workType[$elem['parent_id']][$elem['id']]=$elem['title'];
		}
		else $workType[$elem['parent_id']]=[$elem['id'] => $elem['title']];
	}
}
unset($cat_works);
$workCategory[0]=_("Другое");
foreach ($workType as &$val){
	$val[0]=_("Другое");
}
$vars['row'] = $page;
$vars['brands'] = $settings->autoBrands();
$vars['models'] = $settings->autoModels();
$vars['autoYears'] = $settings->autoYears();
$vars['workCategory'] = $workCategory;
$vars['workType']=$workType;

//для автозаполнения форм (кроме на главной. на главной в settings.php)
$vars['authuser'] = $settings->authuser(); //юзер
if($vars['authuser'])
{
    $vars['myavto'] = $settings->myAuto(); //авто юзера
    //$vars['myorders'] = $settings->myOrders(); //заявки юзера
}
    
?>