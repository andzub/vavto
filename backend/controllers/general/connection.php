<?php


    if( !empty($_POST['upload']) ){

        $files = $files->uploads($_FILES['files'],'images');
        echo json_encode($files);
        exit;
    }

    if( !empty($_POST['ajax']) ):
        echo send($_POST);
        exit;
    endif;
	$vars['block']=$settings->get_page_blocks(['page'=>'connection','lang'=>$config['lang']]);
    function send($data){

        global $config;

        $name_service = htmlspecialchars($data['name_service']);
        $city = htmlspecialchars($data['city']);
        $site = htmlspecialchars($data['site']);

        $name = htmlspecialchars($data['name']);
        if( !$name )
            return _('Укажите Ваше имя.');

        $phone = htmlspecialchars($data['phone']);
        if( !$phone )
            return _('Укажите Ваш номер телефона.');

        $email = htmlspecialchars($data['email']);

        $text = "
            Новая заявка в-автосервис [новый автосервис]:<br><br>
            
            Название автосервиса: ".$name_service."<br>
            Город: ".$city."<br>
            Сайт: ".$site."<br>
            Имя: ".$name."<br>
            Телефон: ".$phone."<br>
            Email: ".$email."<br><br>
            
            ";


        $subject = 'Новая заявка в-автосервис [новый автосервис]';

        $mail = mail::getInstance();
        $mail->send('t@v-avtoservice.com',$subject,$text);

        //Отправляем сообщение клиенту
        $mail->send($email,'В-автосервис',"Спасибо! Ваша заявка успешно отправлена!<br>В самое ближайшее время мы свяжемся с Вами!");


        return 'success';
    }

?>