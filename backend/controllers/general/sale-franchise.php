<?php
    if( $_POST['ajax'] ):
        echo send($_POST);
        exit;
    endif;

    function send($data){
        $city = htmlspecialchars($data['city']);
        $name = htmlspecialchars($data['name']);
        if( !$name )
            return _('Укажите Ваше имя.');

        $phone = htmlspecialchars($data['phone']);
        if( !$phone )
            return _('Укажите Ваш номер телефона.');

        $email = htmlspecialchars($data['email']);

        $text = "
            Новая заявка в-автосервис [ФРАНШИЗА]:<br><br>
            
            Город: ".$city."<br>
            Имя: ".$name."<br>
            Телефон: ".$phone."<br>
            Email: ".$email."<br><br>
            
            ";

        $subject = 'Новая заявка в-автосервис [франшиза]';

        $mail = mail::getInstance();
        $mail->send('franch@centre-mail.ru',$subject,$text);

        //Отправляем сообщение клиенту
        $mail->send($email,'В-автосервис',_("Ваша заявка успешно отправлена!<br>В самое ближайшее время мы свяжемся с Вами!"));
        return 'success';
    }

    $vars['block']=$settings->get_page_blocks(['page'=>'sale-franchise','lang'=>$config['lang']]);
    $vars['articles'] = $api->get('/article/franch/',['lang'=>$config['lang']]);

?>

