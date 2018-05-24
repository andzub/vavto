<?

$sms = sms::getInstance();

class sms {

    //Данные
    private $api_id;
    private $from;

    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {

        //Потом перенести настройки в конфиг
        //global $api_id;

        $api_id = "2144E369-78BA-7ABF-C675-B0956D61160C";
        $from = "hochu-poest";

        $this->api_id = $api_id;
        $this->from = $from;
    }
    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    //Отправить СМС
    public function send($phone,$text){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(

            "api_id" => $this->api_id,
            "to" => $phone,
            "text" => $text,
            "from" => $this->from

        ));
        $body = curl_exec($ch);
        curl_close($ch);
        
        return 'success';


    }

}

?>