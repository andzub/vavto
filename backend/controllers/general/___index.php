<?php
    $stocks = $api->get('/stocks/index/',['lang'=>$config['lang']]);
	$vars['workCategory'] = [];
    $work_cats=$settings->workMainCategory($config['lang']);
    $w_count=count($work_cats);
    $step=ceil (($w_count+1)/3);
    $coll_arr=[];
    for($i=0;$i<$w_count;$i++){
    	if($work_cats[$i]['parent_id']>0)$work_cats[$i]['link']=$settings->getWorkLink($work_cats[$i]['address']).'/';
	    else $work_cats[$i]['link']=$work_cats[$i]['address'].'/';
	    $coll_arr[]=$work_cats[$i];
    	if(count($coll_arr)==$step){
		    $vars['workCategory'][]=$coll_arr;
		    $coll_arr=[];
	    }
    }
	if($coll_arr)$vars['workCategory'][]=$coll_arr;

    $vars['brands'] = $settings->autoBrands();
    $vars['models'] = $settings->autoModels();
    $vars['autoYears'] = $settings->autoYears();
    $vars['workType'] = $settings->worktype();
    $vars['stocks']=$stocks;
	$vars['block']=$settings->get_page_blocks(['page'=>'index','lang'=>$config['lang']]);

    $vars['authuser'] = $settings->authuser(); //юзер
    if($vars['authuser'])
    {
        $vars['myavto'] = $settings->myAuto(); //авто юзера
        //$vars['myorders'] = $settings->myOrders(); //заявки юзера
    }
    
    if( !empty($_POST['ajax']) ):
        echo send($_POST,$vars);
        exit;
    endif;
    
    function send($data,$vars){
        global $config;
	      $model = '';
	      $release_year = '';
	      $work_type='Другое';
	      $category_name='';
        $brand = $vars['brands'][(int)$data['brand']];
        if( !$brand )
            return _('Укажите марку автомобиля.');
				if(isset($vars['models'][$brand['id']][(int)$data['model_'.$brand['id']]])) $model=$vars['models'][$brand['id']][(int)$data['model_'.$brand['id']]];

        if(!empty($data['release_year']) && $data['release_year'] > 1970 && $data['release_year'] < date('Y') ) $release_year=$data['release_year'];

        $vin = (isset($data['vin'])) ? htmlspecialchars($data['vin']) : '';

        if(isset($data['category'])){
	        $category = (int)$data['category'];
	        $category_name = $vars['workCategory'][$category];
	        if( $category_name ){
		        $work_type_id = (int)$data['work_type_'.$data['category']];
		        $work_type = $vars['workType'][$data['category']][$work_type_id];
	        }
        }
        //var_dump($vars['workType'][$work_type_id]);
        //echo $data['category']." ------------ ". $vars['workCategory'][$category]."----$category---";

        echo "category=$category<br />category_name=$category_name<br />work_type_id=$work_type_id<br />work_type=$work_type";

        $city = isset($data['city']) ? htmlspecialchars($data['city']) : '';

        $address = isset($data['address']) ? htmlspecialchars($data['address']) : '';
        $problem = isset($data['problem']) ? htmlspecialchars($data['problem']) : '';
        $stock = isset($data['stock']) ? htmlspecialchars($data['stock']) : '';

        $name = isset($data['name']) ? htmlspecialchars($data['name']) : '';

        $phone = htmlspecialchars($data['phone']);
        if( !$phone )
            return 'Укажите Ваш номер телефона.';

        $email = isset($data['name']) ? htmlspecialchars($data['email']) : '';

        $date = !empty($data['name']) ? htmlspecialchars($data['date']) : '-';

        $time = isset($data['name']) ? htmlspecialchars($data['time']) : '';
        if( $time ) $time = ' в '.$time;



        $text = "
        Новая заявка в-автосервис:<br><br>
        Марка: ".$brand['name']."<br>
        Модель: ".$model['name']."<br>
        Год выпуска: ".$release_year."<br>
        VIN-код: ".$vin."<br>
        Категория: ".$category_name."<br>
        Вид работ: ".$work_type."<br>
        Город: ".$city."<br>
        Район или улица: ".$address."<br>
        Проблема: ".$problem."<br><br>
        Имя: ".$name."<br>
        Телефон: ".$phone."<br>
        Email: ".$email."<br><br>
        Удобно посетить сервис: ".$date." ".$time."<br>
        Акция: ".$stock."<br>
        
        ";

        $subject = 'Новая заявка в-автосервис';

        if( $data['img'] ){
            $text .= '<br><b>Прекрепленные изображения:</b><br>';
            foreach( $data['img'] as $key => $url ){
                $url = $config['protocol'].'://'.$config['domain'].$url;
                $text .= '<a href="'.$url.'">Изображение '.($key+1).'</a><br>';
            }
        }





//если введен емейл  форме
if($email)
{
    // проверяем есть ли такой емейл в базе
    $db = db::getInstance();
    //$email
    $is_set_user=$db->getAll('SELECT id from `client` where `email`=?s' , $email ); //`id`=?i', (int)$id );
    $is_set_user=$is_set_user[0]['id'];
    if(!$is_set_user) // если нет  - регистрируем нового клиента
    {
        $pass = substr(time(),-4);
        $passm=md5($pass."avtoservis");
        $date=date("Y-m-d", time());
        
        $set_user=  $db->query('insert into client (name, email, password, tel, date, status, purse)
                    values ( "'.$name.'",  "'.$email.'",  "'.$passm.'",  "'.$phone.'", "'.$date.'", 1, 500 )'  );
        $is_set_user=$db->mysql_insert_id();

        //формируем сообщения
        $admin_text="<br /><br />Зарегистрирован как новый клиент: $email";
        $clinet_text="<br /><br />Вы успешно зарегистрированы! Для входа в личный кабинет используйте:<br /> Login: ".$email."<br />Пароль: ".$pass;
    }
}

        $mail = mail::getInstance();
        //$mail->send('s@v-avtoservice.com',$subject,$text);
        //$mail->send('slave919@gmail.com',$subject,$text.$admin_text);
        //Отправляем сообщение клиенту
        //$mail->send($email,'В-автосервис',"{%trans%}Спасибо! Ваша заявка успешно отправлена!<br>В самое ближайшее время мы свяжемся с Вами!{%endtrans%}". $clinet_text);
// клиент $mail->send($email,'В-автосервис',"Спасибо! Ваша заявка успешно отправлена!<br>В самое ближайшее время мы свяжемся с Вами!". $clinet_text);


        
		//пишем заявку в базу
  //echo "category = $category ; work_type_id=$work_type_id; work_type = $work_type;";
        if($work_type_id) $category=$work_type_id; // ИД категории/вида работ
        //unix time
        //получить время
        //echo $data['date']." / ".$data['time'];
        //exit;

        $dt=explode('-',$data['date']); //g=0 m=1 d=2
        $tm=explode(':',$data['time']); //m d y
        $date_udobno=mktime($tm[0], $tm[1], 0 ,$dt[1], $dt[2], $dt[0]);
        
        //корректируем для формы шиномонтажа. диаметр диска добавить в описание проблемы
        if($data['diametr_disk']) $problem="Диаметр диска:".$data['diametr_disk']."\n\r.".$problem;

        $data_db = array(
            'id_client' => $is_set_user,
            'cl_name' => $name,
            'cl_tel' => $phone,
            'cl_email' => $email,
            'cl_city' => $city,
            'cl_rayon' => $address,
            'date_add' => time(),
            'category' => $category,
            'problem' => $problem,
            'service' => '', //сервис, который будет обслуживать заявку добавит менеджер
            'date_udobno' => $date_udobno, //$data['date']." - ".$data['time'], //2018-05-09 - 18:00
            'car_marka' => $data['brand'], // $brand['name'],
            'car_model' => $data['model_'.$data['brand']], //$model['name'],
        
            'car_vin' => $vin,
            'car_year' => $release_year,
            
            'price_work' => 0,
            'price_detail' => 0,
            'bonus' => 0,
            'otziv' => '',
            'status' => 1 // 1- новый, 2 - в работе, 3 -выполнен, 4 - отменен
        );
        global $settings;
        $is_added=$settings->add_history($data_db);
        var_dump($data_db);
        

        //Отправляем данные в CRM
        if( $curl = curl_init() ) {
            $url = 'http://avtoservice.mfucentr.ru/roistat/lead_add';
            $data = array(
                'firstname' => $name,
                'phone' => $phone,
                'adr_email' => $email,
                'car_marka' => $brand['name'],
                'car_model' => $model['name'],
                'car_year' => $release_year,
                'vin_kod' => $vin,
                'category' => $category_name,
                'kind_of_work' => $work_type,
                'address' => $address,
                'city' => $city,
                'cond_where' => $date." ".$time,
                'problem' => $problem
            );
            curl_setopt($curl, CURLOPT_URL, $url.'?'.http_build_query($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = curl_exec($curl);
            curl_close($curl);
        }

        return 'success';
    }

?>