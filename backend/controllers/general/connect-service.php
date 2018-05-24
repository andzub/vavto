<?php

    if( $_POST['ajax'] ):
        echo send($_POST);
        exit;
    endif;
	$vars['block']=$settings->get_page_blocks(['page'=>'connect-service','lang'=>$config['lang']]);
    function send($data){

        $name_service = htmlspecialchars($data['name_service']);
        $address_service = htmlspecialchars($data['address_service']);
        $site = htmlspecialchars($data['site']);
        $specialization = htmlspecialchars($data['specialization']);

        $fio = htmlspecialchars($data['fio']);
        if( !$fio )
            return 'Укажите Ваше ФИО.';

        $phone = htmlspecialchars($data['phone']);
        if( !$phone )
            return 'Укажите Ваш номер телефона.';

        $email = htmlspecialchars($data['email']);

        $text = "
                Новая заявка в-автосервис [партнерка]:<br><br>
                
                Название автосервиса: ".$name_service."<br>
                Адрес автосервиса: ".$address_service."<br>
                Сайт: ".$site."<br>
                Специализация: ".$specialization."<br>
                ФИО: ".$fio."<br>
                Телефон: ".$phone."<br>
                Email: ".$email."<br><br>
                
                ";

        $subject = 'Новая заявка в-автосервис [партнерка]';

        $mail = mail::getInstance();
        $mail->send('t@v-avtoservice.com',$subject,$text);

        //Отправляем сообщение клиенту
        $mail->send($email,'В-автосервис',"Спасибо! Ваша заявка успешно отправлена!<br>В самое ближайшее время мы свяжемся с Вами!");


        return 'success';
    }

?>