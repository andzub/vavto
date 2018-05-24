<?

$mail = mail::getInstance();
class mail {
	
    protected static $_instance;
	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {} 
    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
		return self::$_instance;
    }
	
	/*Отправить сообщение*/
	public function send($email,$subject,$text,$files=false){


		if( !preg_match("/^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}$/u", $email) )
			return false;

		// пример использования
		require_once("backend/components/plugins/smtp.php");

		$mailSMTP = new SendMailSmtpClass('vitenadoviti121@gmail.com', 'erbtrb5644567fb', 'ssl://smtp.gmail.com', 'Хочу-поесть', 465);
		  
		// $mailSMTP = new SendMailSmtpClass('dev.molchkov@yandex.ru', 'td34s77ji', 'ssl://smtp.yandex.ru', 'Хочу-поесть', 465);
		// $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'имя отправителя');
		  
		// заголовок письма
		$headers= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
		$headers .= "From: В-автосервис <dev.molchkov@yandex.ru>\r\n"; // от кого письмо

        if( $files ){

            $separator = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$separator}x";
            $eol = PHP_EOL;

            foreach( $files as $key => $file ){

                $filetype = 'image/jpeg';
                $filename = 'File '.($key+1);

                $content = chunk_split(base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$file)));
                $headers .= '--' . $separator . $eol;
                $headers .= 'Content-Type: '.$filetype.'; name=' . $filename . $eol;
                $headers .= 'Content-Transfer-Encoding: base64' . $eol;
                $headers .= 'Content-Disposition: attachment; filename=' . $filename . $eol . $eol;
                $headers .= $content . $eol . $eol;
                $headers .= '--' . $mime_boundary . $eol;

            }

        }


		$result =  $mailSMTP->send($email, $subject, $text, $headers); // отправляем письмо
		// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Заголовки письма');
		if($result === true){
		    return "Письмо успешно отправлено";
		}else{
		    return "Письмо не отправлено. Ошибка: " . $result;
		}
	}

	
}

?>