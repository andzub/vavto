<?php
    //Пароль
    global $config;
    if ( !isset($_SERVER['PHP_AUTH_USER'] )) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Ошибка доступа!';
        exit;
    } else {
        if( ($_SERVER['PHP_AUTH_USER'] != $config['admin_login'] || $_SERVER['PHP_AUTH_PW'] != $config['admin_pass']) && $_COOKIE['admin_auth'] != md5($config['admin_login'].$config['admin_pass']) ){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Ошибка доступа!';
            exit;
        }else{
            $time_cookies = mktime(0,0,0,1,1,2030);
            setcookie('admin_auth', md5($config['admin_login'].$config['admin_pass']), $time_cookies, "/", ".".$config['domain']);
        }
    }

    if( isset($this->GET[0]) && $this->GET[0] == 'add' ){
        if( !empty($_POST['ajax']) ){
            $_POST['img'] = $_FILES['img'];
            $_POST['img_min'] = $_FILES['img_min'];
	        $_POST['img_icon'] = $_FILES['img_icon'];
            $response = $api->post('/cat/',$_POST);
            echo json_encode($response);
            exit;
        }
        $row=[];
	    $row['cats'] = $db->getAll('SELECT c.id,c.address,cl.title FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang="ru") WHERE type=1 AND parent_id=0 ORDER BY title');
	    $row['forms']=$settings->formList ();
        $template = 'cats-add.html';
    }

    if( isset($this->GET[0]) && $this->GET[0] == 'edit' ){
        if( !empty($_POST['ajax']) ){
            $_POST['img'] = $_FILES['img'];
            $_POST['img_min'] = $_FILES['img_min'];
	        $_POST['img_icon'] = $_FILES['img_icon'];
            $response = $api->put('/cat/',$_POST);
            echo json_encode($response);
            exit;
        }
	    if(!empty($this->GET[2]) && isset($config['langs'][$this->GET[2]])) {
            $cur_lang=$this->GET[2];
            $get_data=['id'=>$this->GET[1],'lang'=>$this->GET[2]];
	    }
	    else {
		    $cur_lang='ru';
		    $get_data=['id'=>$this->GET[1],'lang'=>'ru'];
	    }
        $row = $api->get('/cat/', $get_data);
	    if( !$row ){
            header('Location: /admin/cats/');
            exit;
        }
	    if($cur_lang==='ru') $row['is_main_lang']=1;//Если русский, выводим все, иначе только то что нужно для перевода
	    $row['cur_lang']=$cur_lang;

	    $row['cats'] = $db->getInd('id','SELECT c.id,c.address,cl.title
        FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang="ru")
        WHERE type=1 AND parent_id=0 AND c.`id`!=?i
        ORDER BY cl.title',$row['id']);
        
        $row['parrent']['id']=$row['cats'][$row['parent_id']]['id'];
        $row['parrent']['title']=$row['cats'][$row['parent_id']]['title'];
        $row['parrent']['address']=$row['cats'][$row['parent_id']]['address'];
        if($row['parrent']['address']) $row['parrent']['address'].="/";

	    $row['forms']=$settings->formList ();
        $template = 'cats-edit.html';

    }

    //удаление категории в админке
    if( isset($this->GET[0]) && $this->GET[0] == 'delete' ){

        $api->delete('/cat/',$this->GET[1]);
        header('Location: /admin/cats/');
        exit;
    }

    
    if(isset($row)) $vars['row']=$row;
    if(empty($this->GET)) { //для списка категорий в админке

        $cats=$api->get('/cat/allsort/');
        $no_cat_works=['id'=>0,'address'=>'','title'=>'Без категории','works'=>[],'link'=>''];
        foreach ($cats as $cat){
            if($cat['type']==1){
	            $cat['link']='/'.$cat['address'].'/';
                $cat_array[$cat['id']]=$cat;
            }
            elseif($cat['type']==2){
	            if(isset($cat_array[$cat['parent_id']])){
		            $cat['link']='/'.$cat_array[$cat['parent_id']]['address'].'/'.$cat['address'].'/';
	                if(empty($cat_array[$cat['parent_id']]['works'])) $cat_array[$cat['parent_id']]['works']=[];
		            $cat_array[$cat['parent_id']]['works'][]=$cat;
                }
                else {
	                $cat['link']='/'.$cat['address'].'/';
	                $no_cat_works['works'][]=$cat;
                }
            }
	        //$cat['link']=($cat['parent_address']) ? $cat['parent_address'].'/'.$cat['address'] : $cat['address'];
        }
	    $cat_array[]=$no_cat_works;
	    $vars['rows']=$cat_array;
    }
?>
