<?php

$controller = controller::getInstance();
class controller {
    private $this_file;
    private $this_file_last;
    private $this_dir_last;
    private $GET;
    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}
    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    /*Инициализация контролера*/
    public function init($url=false){
        global $config;
        header('Content-Type: text/html; charset=utf-8');
        session_start();
        ini_set("session.use_cookies","1");
        session_regenerate_id(true);
        ini_set('session.cookie_domain', '.'.$config['domain'] );
        //echo '.'.$config['domain'];
        if( $config['redirect'] )
            $this->redirect();

        $core = core::getInstance();
        $require = $core->getRequire();
        foreach( $require as $class )
            ${$class} = $class::getInstance();

        $default = "index";
        if($url == false){
            $url = $_SERVER['REQUEST_URI'];
        }
        if($url === "/" ){$url = $default;}
				elseif($_GET) $url=explode('?',$url)[0];//Берем без get

	    $root = $_SERVER['DOCUMENT_ROOT'];
     $url_parts = explode('/', trim($url, ' /'));
	    $vars = [];
	    $config['langs']=$settings->langList();
	    $site_options=$settings->get_site_options();
	    $config['default_lang']=(!empty($site_options['default_lang']['val'])) ? $site_options['default_lang']['val'] : 'ru';
	    if( isset($config['langs'][$url_parts[0]]) ){
		    $config['lang']=$url_parts[0];
		    array_shift($url_parts);
	    }
	    else $config['lang']=$config['default_lang'];
	    $trans_domain=$settings->get_trans_domain($config['langs'][$config['lang']]);

	    $settings->setTranslate($config['langs'][$config['lang']]['locale'],$trans_domain);
	    $vars['default_lang']=$config['default_lang'];
	    $vars['lang']=$config['lang'];
	    $vars['langs']=$config['langs'];
	    if(count($url_parts)===0){$url_parts = [$default];}

        $full_url_parts=$url_parts;
        $array_shift = array_shift($url_parts);
        if(!$array_shift) $array_shift='index';

        if(is_dir('backend/controllers/'.$array_shift)){
            $array_shift_two = array_shift($url_parts);
            if(is_dir('backend/controllers/'.$array_shift.'/'.$array_shift_two)){
                $file = array_shift($url_parts);
                if($file === NULL){ $file = 'index';}
                $url_controller = 'backend/controllers/'.$array_shift.'/'.$array_shift_two.'/'.$file.'.php';
                $template = $array_shift.'/'.$array_shift_two.'/'.$file.'.html';
                $this->this_dir_last = $array_shift;
            }else{
                $file = $array_shift_two;
                if($file === NULL){ $file = 'index';}
                $url_controller = 'backend/controllers/'.$array_shift.'/'.$file.'.php';
                $template = ''.$array_shift.'/'.$file.'.html';
                $this->this_dir_last = $array_shift;
            }
        }else{
            $file = $array_shift;
            $url_controller = 'backend/controllers/general/'.$file.'.php';
            $template = 'general/'.$file.'.html';
        }


        //Если файл не найден
        if(!is_file($url_controller)){
            //Передаем строку и проверяем, нет ли хранения в БД
            $rout = $routing->init($full_url_parts);
            if( $rout ){
                $url_controller = $rout['controller'];
                $template = (isset($rout['template'])) ? $rout['template'] : '';
            }else{
                $url_controller = 'backend/controllers/general/404.php';
                $template = 'general/404.html';
            }
        }

        //Если это разработка
        if( $config['debug'] ):
            $url = $_SERVER['REQUEST_URI'];
            if( mb_substr($url,0,5) == '/_dev' ):
                if( $url_parts[0] ){
                    $file = array_shift($url_parts);
                }else{
                    $file = 'index';
                }
                $config['templates_dir'] = array('_dev/views');
                $url_controller = '_dev/controllers'.$systemtype.'/'.$file.'.php';
                $template = $file.'.html';
            endif;
        endif;

        $this->GET = $url_parts;
        $this->this_file = $url_controller;
        $this->this_file_last = $file;

        //Включаем работу API на контроллер
        $api->set_is_controller(true);

        //Проверка токена и создание его
        if( count($_REQUEST) > 0 ){
            if( empty($_REQUEST['token']) )
                $_REQUEST['token'] = false;
            if( !$token->check($_REQUEST['token']) )
                $api->set_check_token(false);

        }
        $_token = $token->get();
        if( is_file($url_controller) ){
            require_once($url_controller);
        }else{
            $errorf->_print('Создайте файл контроллера: '.$url_controller);
        }
        //Выключаем работу API на контроллер
        $api->set_is_controller(false);
	    //$city = $this->subdomain();





        //Добавляем токен в переменную vars
        $vars['token'] = $_token;
        $vars['session'] = $_SESSION;
        if(isset($_SESSION['city']['id'])) {
            $vars['city'] = $_SESSION['city'];
        } else {
            $vars['city']['id'] = 5054;

            //язык столицы
            if($config['lang']=='ru') $vars['city']['name'] = 'Москва';
            else     $vars['city']['name'] = 'Moskow';
        }

        //+  ссылка на языковые версии
        if($config['lang']=='ru')      $vars['langlink'] = $config['protocol'].'://'.$config['domain'].'';
        else    $vars['langlink'] = $config['protocol'].'://'.$config['domain'].'/en';
        if($vars['langlink']) $_SESSION['langlink']=$vars['langlink']; //путь с учетом языка. для уведомлений на емейл о заявках
        else unset($_SESSION['langlink']);




		$vars['u']=$_SERVER['REQUEST_URI'];


        $geo = geo::getInstance();
        $vars['city_all'] = json_decode($geo->getAllCity($config['lang']),1);


    if(!$vars['authuser']['gorod']) //для подстановки в формы ГЕО Города, через authuser
    {
        $vars['authuser']['gorod_id']=$vars['city']['id'];//из токена
        $vars['authuser']['gorod']=$vars['city']['name'];//из токена
        $vars['authuser']['token']='zaglushka';//из токена
    }

//var_dump($vars);



	    return array(
            'template' => $template,
            'vars' => $vars
        );
    }

    //Инициализация контролера [ тестовый режим ]
    public function init_debug($url=false){
        header('Content-Type: text/html; charset=utf-8');
        session_start();
        global $config;
        if( $config['redirect'] )
            $this->redirect();
        $core = core::getInstance();
        $require = $core->getRequire();
        foreach( $require as $class )
            ${$class} = $class::getInstance();
        if($url == false)
            $url = $_SERVER['REQUEST_URI'];
        if($url == "/")
            $url = "index";
        $url_parts = explode('/', trim($url, ' /'));
        $full_url_parts=$url_parts;
        $array_shift = array_shift($url_parts);
        if(is_dir('backend/controllers/'.$array_shift)){
            $array_shift_two = array_shift($url_parts);
            if(is_dir('backend/controllers/'.$array_shift.'/'.$array_shift_two)){
                $file = array_shift($url_parts);
                if($file === NULL){ $file = 'index';}
                $url_controller = 'backend/controllers/'.$array_shift.'/'.$array_shift_two.'/'.$file.'.php';
                $template = $array_shift.'/'.$array_shift_two.'/'.$file.'.html';
                $this->this_dir_last = $array_shift;
            }else{
                $file = $array_shift_two;
                if($file === NULL){ $file = 'index';}
                $url_controller = 'backend/controllers/'.$array_shift.'/'.$file.'.php';
                $template = ''.$array_shift.'/'.$file.'.html';
                $this->this_dir_last = $array_shift;
            }
        }else{
            $file = $array_shift;
            $url_controller = 'backend/controllers/general/'.$file.'.php';
            $template = 'general/'.$file.'.html';
        }

        //Если файл не найден
        if(!is_file($url_controller)){
            //Передаем строку и проверяем, нет ли хранения в БД
            $rout = $routing->init($_SERVER['REQUEST_URI'],$url_parts);
            if( $rout ){
                $url_controller = $rout['controller'];
                $template = $rout['template'];
            }else{
                $url_controller = 'backend/controllers/general/404.php';
                $template = 'general/404.html';
            }
        }

        $url = $_SERVER['REQUEST_URI'];
        if( mb_substr($url,0,5) == '/_dev' ):

            if( $url_parts[0] ){
                $file = array_shift($url_parts);
            }else{
                $file = 'index';
            }

            $config['templates_dir'] = array('_dev/views');
            $url_controller = '_dev/controllers/'.$file.'.php';
            $template = $file.'.html';

        endif;

	    $vars = array();
        $this->GET = $url_parts;
        $this->this_file = $url_controller;
        $this->this_file_last = $file;


        //Включаем работу API на контроллер
        $api->set_is_controller(true);

        //Проверка токена и создание его
        if( count($_REQUEST) > 0 ){

            if( !$token->check($_REQUEST['token']) )
                $api->set_check_token(false);

        }

        $_token = $token->get();

        if( is_file($url_controller) ){
            require_once($url_controller);
        }else{
            $errorf->_print('Создайте файл контроллера: '.$url_controller);
        }

        //Выключаем работу API на контроллер
        $api->set_is_controller(false);

        //Добавляем токен в переменную vars
        $vars['token'] = $_token;
        $vars['session'] = $_SESSION;

        return array(
            'template' => $template,
            'vars' => $vars
        );

    }

    //Редирект со слешом на конце
    public function redirect(){

        if( mb_substr($_SERVER['REQUEST_URI'],-1) != '/' && (!$_POST && !$_GET) ){
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$_SERVER['REQUEST_URI'].'/');
            exit;
        }

    }

    /*Возвратить текущий файл контроллера*/
    public function this_file (){
        return $this->this_file;
    }

    /*Возвратить текущий конечый файл контроллера*/
    public function this_file_last (){
        return $this->this_file_last;
    }
    /*Возвратить текущий конечыую дириктрию*/
    public function this_dir_last (){
        return $this->this_dir_last;
    }

    /*Возвратить GET*/
    public function GET(){
        return $this->GET;
    }


    /*Получить адрес станицы*/
    function getAddress($url){
        $url = explode('/',$url);
        unset($url[0]);
        unset($url[1]);
        unset($url[2]);
        return implode('/',$url);
    }

    /*Инициализация контролера*/
    public function subdomain(){
	    global $config;
        $user = user::getInstance();
        $db = db::getInstance();
	    $subdomain = explode('.', $_SERVER['HTTP_HOST']);
	    //Пока что переводим на москву для тестового
	    if($subdomain[0]=='gg' || $subdomain[1]=='gg'){
		    return $db->getRow('SELECT c.*,cl.title FROM `geo_city` c INNER JOIN geo_city_lang cl ON (c.id = cl.city_id AND cl.lang=?s)  WHERE `id`=5054',$config['lang']);
	    }
	    $city = $user->getCity();
        $domain = $subdomain[1];
        if(isset($subdomain[2])) $domain .= '.'.$subdomain[2];
        $url = $_SERVER['REQUEST_URI'];

        if( count($subdomain) > 2) {

            $subdomain = array_shift($subdomain);
            $subdomain = $this->DecodePunycodeIDN($subdomain);

            if( (mb_strtolower($subdomain) != mb_strtolower($city['name'])) && (mb_strtolower(str_replace('-', ' ', $subdomain)) != mb_strtolower(str_replace('-', ' ', $city['name']))) ){

                $city = $db->getRow('SELECT * FROM `geo_city` WHERE `name`=?s OR `name`=?s LIMIT 1', $subdomain, str_replace('-', ' ', $subdomain) );
                if( $city ){

                    $user->changeCity($city['id']);

                    if( $city['name'] == 'Москва' ){
                        header('Location: http://'.$domain.$url);
                        exit;
                    }

                    $city['name'] = str_replace(' ', '-', $city['name']);
                    header('Location: http://'.mb_strtolower($city['name']).'.'.$domain.$url);
                    exit;

                }else{

                    header('Location: http://'.$domain);
                    exit;
                }

            //Если нужно переадресация на москву
            }else{


                if( mb_strtolower($subdomain) == 'москва' ){
                    header('Location: http://'.$domain.$url);
                    exit;
                }

            }

        }else{


            if( $city['name'] != 'Москва' ){

                if( $_COOKIE['changeMoscow'] == true || $_SESSION['changeMoscow'] == true || $_GET['changeMoscow'] ){

                    $city = $user->changeCity(5054);
                    //$city['name'] = str_replace(' ', '-', $city['name']);
                    //header('Location: http://'.mb_strtolower($city['name']).'.'.$_SERVER['HTTP_HOST'].$url);

                }else{

                    $city['name'] = str_replace(' ', '-', $city['name']);

                    //echo 'Location: http://'.mb_strtolower($city['name']).'.'.$_SERVER['HTTP_HOST'].$url; exit;
                    header('Location: http://'.mb_strtolower($city['name']).'.'.$_SERVER['HTTP_HOST'].$url);
                    exit;

                }
            }

        }


        return $city;
    }

    function ordUTF8($c, $index = 0, &$bytes = null)
    {
        $len = strlen($c);
        $bytes = 0;
        if ($index >= $len)
            return false;
        $h = ord($c{$index});
        if ($h <= 0x7F) {
            $bytes = 1;
            return $h;
        }
        else if ($h < 0xC2)
            return false;
        else if ($h <= 0xDF && $index < $len - 1) {
            $bytes = 2;
            return ($h & 0x1F) << 6 | (ord($c{$index + 1}) & 0x3F);
        }
        else if ($h <= 0xEF && $index < $len - 2) {
            $bytes = 3;
            return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
                | (ord($c{$index + 2}) & 0x3F);
        }
        else if ($h <= 0xF4 && $index < $len - 3) {
            $bytes = 4;
            return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                | (ord($c{$index + 2}) & 0x3F) << 6
                | (ord($c{$index + 3}) & 0x3F);
        }
        else
            return false;
    }

    /**
     * Encode UTF-8 domain name to IDN Punycode
     *
     * @param string $value Domain name
     * @return string Encoded Domain name
     *
     * @author Igor V Belousov <igor@belousovv.ru>
     * @copyright 2013, 2015 Igor V Belousov
     * @license http://opensource.org/licenses/LGPL-2.1 LGPL v2.1
     * @link http://belousovv.ru/myscript/phpIDN
     */
    function EncodePunycodeIDN( $value )
    {
        /* search subdomains */
        $sub_domain = explode( '.', $value );
        if ( count( $sub_domain ) > 1 ) {
            $sub_result = '';
            foreach ( $sub_domain as $sub_value ) {
                $sub_result .= '.' . EncodePunycodeIDN( $sub_value );
            }
            return substr( $sub_result, 1 );
        }

        /* http://tools.ietf.org/html/rfc3492#section-6.3 */
        $n      = 0x80;
        $delta  = 0;
        $bias   = 72;
        $output = array();

        $input  = array();
        $str    = $value;
        while ( mb_strlen( $str , 'UTF-8' ) > 0 )
        {
            array_push( $input, mb_substr( $str, 0, 1, 'UTF-8' ) );
            $str = mb_substr( $str, 1, null, 'UTF-8' );
        }

        /* basic symbols */
        $basic = preg_grep( '/[\x00-\x7f]/', $input );
        $b = $basic;

        if ( $b == $input )
        {
            return $value;
        }
        $b = count( $b );
        if ( $b > 0 ) {
            $output = $basic;
            /* add delimeter */
            $output[] = '-';
        }
        unset($basic);
        /* add prefix */
        array_unshift( $output, 'xn--' );

        $input_len = count( $input );
        $h = $b;

        $ord_input = array();

        while ( $h < $input_len ) {
            $m = 0x10FFFF;
            for ( $i = 0; $i < $input_len; ++$i )
            {
                $ord_input[ $i ] = ordUtf8( $input[ $i ] );
                if ( ( $ord_input[ $i ] >= $n ) && ( $ord_input[ $i ] < $m ) )
                {
                    $m = $ord_input[ $i ];
                }
            }
            if ( ( $m - $n ) > ( 0x10FFFF / ( $h + 1 ) ) )
            {
                return $value;
            }
            $delta += ( $m - $n ) * ( $h + 1 );
            $n = $m;

            for ( $i = 0; $i < $input_len; ++$i )
            {
                $c = $ord_input[ $i ];
                if ( $c < $n )
                {
                    ++$delta;
                    if ( $delta == 0 )
                    {
                        return $value;
                    }
                }
                if ( $c == $n )
                {
                    $q = $delta;
                    for ( $k = 36;; $k += 36 )
                    {
                        if ( $k <= $bias )
                        {
                            $t = 1;
                        }
                        elseif ( $k >= ( $bias + 26 ) )
                        {
                            $t = 26;
                        }
                        else
                        {
                            $t = $k - $bias;
                        }
                        if ( $q < $t )
                        {
                            break;
                        }
                        $tmp_int = $t + ( $q - $t ) % ( 36 - $t );
                        $output[] = chr( ( $tmp_int + 22 + 75 * ( $tmp_int < 26 ) ) );
                        $q = ( $q - $t ) / ( 36 - $t );
                    }

                    $output[] = chr( ( $q + 22 + 75 * ( $q < 26 ) ) );
                    /* http://tools.ietf.org/html/rfc3492#section-6.1 */
                    $delta = ( $h == $b ) ? $delta / 700 : $delta>>1;

                    $delta += intval( $delta / ( $h + 1 ) );

                    $k2 = 0;
                    while ( $delta > 455 )
                    {
                        $delta /= 35;
                        $k2 += 36;
                    }
                    $bias = intval( $k2 + 36 * $delta / ( $delta + 38 ) );
                    /* end section-6.1 */
                    $delta = 0;
                    ++$h;
                }
            }
            ++$delta;
            ++$n;
        }
        return implode( '', $output );
    }

    /**
     * Decode IDN Punycode to UTF-8 domain name
     *
     * @param string $value Punycode
     * @return string Domain name in UTF-8 charset
     *
     * @author Igor V Belousov <igor@belousovv.ru>
     * @copyright 2013, 2015 Igor V Belousov
     * @license http://opensource.org/licenses/LGPL-2.1 LGPL v2.1
     * @link http://belousovv.ru/myscript/phpIDN
     */
    function DecodePunycodeIDN( $value )
    {
        /* search subdomains */
        $sub_domain = explode( '.', $value );
        if ( count( $sub_domain ) > 1 ) {
            $sub_result = '';
            foreach ( $sub_domain as $sub_value ) {
                $sub_result .= '.' . DecodePunycodeIDN( $sub_value );
            }
            return substr( $sub_result, 1 );
        }

        /* search prefix */
        if ( substr( $value, 0, 4 ) != 'xn--' )
        {
            return $value;
        }
        else
        {
            $bad_input = $value;
            $value = substr( $value, 4 );
        }

        $n      = 0x80;
        $i      = 0;
        $bias   = 72;
        $output = array();

        /* search delimeter */
        $d = strrpos( $value, '-' );

        if ( $d > 0 ) {
            for ( $j = 0; $j < $d; ++$j) {
                $c = $value[ $j ];
                $output[] = $c;
                if ( $c > 0x7F )
                {
                    return $bad_input;
                }
            }
            ++$d;
        } else {
            $d = 0;
        }

        while ($d < strlen( $value ) )
        {
            $old_i = $i;
            $w = 1;

            for ($k = 36;; $k += 36)
            {
                if ( $d == strlen( $value ) )
                {
                    return $bad_input;
                }
                $c = $value[ $d++ ];
                $c = ord( $c );

                $digit = ( $c - 48 < 10 ) ? $c - 22 :
                    (
                    ( $c - 65 < 26 ) ? $c - 65 :
                        (
                        ( $c - 97 < 26 ) ? $c - 97 : 36
                        )
                    );
                if ( $digit > ( 0x10FFFF - $i ) / $w )
                {
                    return $bad_input;
                }
                $i += $digit * $w;

                if ( $k <= $bias )
                {
                    $t = 1;
                }
                elseif ( $k >= $bias + 26 )
                {
                    $t = 26;
                }
                else
                {
                    $t = $k - $bias;
                }
                if ( $digit < $t ) {
                    break;
                }

                $w *= 36 - $t;

            }

            $delta = $i - $old_i;

            /* http://tools.ietf.org/html/rfc3492#section-6.1 */
            $delta = ( $old_i == 0 ) ? $delta/700 : $delta>>1;

            $count_output_plus_one = count( $output ) + 1;
            $delta += intval( $delta / $count_output_plus_one );

            $k2 = 0;
            while ( $delta > 455 )
            {
                $delta /= 35;
                $k2 += 36;
            }
            $bias = intval( $k2 + 36  * $delta / ( $delta + 38 ) );
            /* end section-6.1 */
            if ( $i / $count_output_plus_one > 0x10FFFF - $n )
            {
                return $bad_input;
            }
            $n += intval( $i / $count_output_plus_one );
            $i %= $count_output_plus_one;
            array_splice( $output, $i, 0,
                html_entity_decode( '&#' . $n . ';', ENT_NOQUOTES, 'UTF-8' )
            );
            ++$i;
        }

        return implode( '', $output );
    }

}

?>