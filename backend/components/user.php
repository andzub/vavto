<?

$user = user::getInstance();

class user {

	public $data;
	public $auth = NULL;
	protected static $_instance;
	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {}
	public static function getInstance() {
		if (self::$_instance === null) { self::$_instance = new self; }
		return self::$_instance;
	}

	// //Проверка авторизован ли юзер и получения данных о нем
	/* public function info(){
	 	$bd = bd::getInstance();


	 	//Если уже проверка была - возвращаем результат
	 	if( $this->auth !== NULL)
	 		return $this->data;

	 	//Если неправильный ХЭШ в куках - возвращаем результат
	 	if( mb_strlen($_COOKIE['hash']) != 32 || (int)$_COOKIE['user_id'] < 1 ):
	 		$this->auth = false;
	 		$this->data = false;
	 		return false;
	 	endif;

	 	$info = $bd->getRow('SELECT * FROM `users` WHERE `id`=?i AND `hash`=?s LIMIT 1',$_COOKIE['user_id'],$_COOKIE['hash']);
	 	if(!$info):
	 		$this->auth = false;
	 		$this->data = false;
	 		return false;
	 	endif;

	 	if( $info['access'] == 'user' ){
	 	    $info['client'] = $bd->getRow('SELECT * FROM `clients` WHERE `id`=?i',$info['id']);

         }elseif( $info['access'] == 'restaurant' ){
             $info['restaurant'] = $bd->getRow('SELECT * FROM `restaurants` WHERE `id`=?i',$info['id']);

         }elseif( $info['access'] == 'partner' ){
             $info['partner'] = $bd->getRow('SELECT * FROM `partners` WHERE `id`=?i',$info['id']);
         }

	 	$this->auth = true;
	 	$this->data = $info;
	 	return true;
	 }*/

	

	// //Авторизация
	// public function login ($data){
	// 	$bd = bd::getInstance();
 //        global $config;

	// 	$user = $bd->getRow('SELECT * FROM users WHERE login=?s AND pass=?s', $data['login'], md5(md5($data['pass'])) );

	// 	if( !$user )
	// 		return 'Неверная электронная почта или пароль.';

	// 	if( mb_strlen($user['hash']) != 32 ):
	// 		$hash = md5(time().rand());
	// 		$bd->query('UPDATE `users` SET `hash`=?s WHERE `id`=?i',$hash,$user['id']);
	// 	else:
	// 		$hash = $user['hash'];
	// 	endif;

	// 	$this->data = $user;

	// 	$time_cookies = mktime(0,0,0,1,1,2030);
	// 	setcookie('hash', $hash, $time_cookies, "/", ".".$config['domain']);
	// 	setcookie('user_id', $user['id'], $time_cookies, "/", ".".$config['domain']);

	// 	return 'success';
	// }



 //    //Восстановить пароль ШАГ 1
 //    public function restore_one ($data){
 //        $bd = bd::getInstance();
 //        $mail = mail::getInstance();
 //        $sms = sms::getInstance();

 //        $login = htmlspecialchars(trim($data['login']));

 //        $user_data = $bd->getRow('SELECT * FROM `users` WHERE `login`=?s',$login);
 //        if( !$user_data ){
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Данный логин не найден в базе данных.'
 //                )
 //            );
 //        }

 //        $code = rand(10000,90000);
 //        $_SESSION['code_restore'] = $code;
 //        $_SESSION['id_restore'] = $user_data['id'];
 //        $_SESSION['hash_restore'] = md5($code.$user_data['id'].'DF*#@');

 //        if( $user_data['access'] == 'user' ){

 //            $info = $bd->getRow('SELECT * FROM `clients` WHERE `id`=?i',$user_data['id']);
 //            $email = $info['email'];
 //            $phone = $info['phone'];

 //        }elseif( $user_data['access'] == 'restaurant' ){

 //            $info = $bd->getRow('SELECT * FROM `restaurants` WHERE `id`=?i',$user_data['id']);
 //            $email = $info['email'];
 //            $phone = $info['phone'];

 //        }elseif( $user_data['access'] == 'franchise' ){

 //            $info = $bd->getRow('SELECT * FROM `franchise` WHERE `id`=?i',$user_data['id']);
 //            $email = $info['email'];
 //            $phone = $info['phone'];

 //        }else{
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Вы не можете восстановить пароль для данного логина!'
 //                )
 //            );
 //        }

 //        $text = "Ваш код для восстановления пароля на хочу-поесть: ".$code." Если Вы не пытаетесь восстановить пароль, проигнорируйте данное сообщение!";
 //        $mail->send($email,"Восстановление пароля",$text);
 //        $sms->send($phone,$text);

 //        $_SESSION['phone_restore'] = $phone;

 //        return json_encode(
 //            array(
 //                'status' => 'success_one'
 //            )
 //        );
 //    }



 //    //Восстановить пароль ШАГ 2
 //    public function restore_two ($data){
 //        $bd = bd::getInstance();
 //        $sms = sms::getInstance();

 //        if( $_SESSION['code_restore'] != $data['code'] ){
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Неверный код доступа!'
 //                )
 //            );
 //        }

 //        if( md5($data['code'].$_SESSION['id_restore'].'DF*#@') != $_SESSION['hash_restore'] ){
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Неверный код доступа!'
 //                )
 //            );
 //        }

 //        $pass = $data['pass'];
 //        $pass_two = $data['pass_two'];

 //        if( mb_strlen($pass) < 6 ){
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Новый пароль не должен быть менее 6 символов!'
 //                )
 //            );
 //        }

 //        if( $pass != $pass_two ){
 //            return json_encode(
 //                array(
 //                    'error' => true,
 //                    'text' => 'Новый пароль и повторение нового пароля не совпадают!'
 //                )
 //            );
 //        }

 //        $new_pass = md5(md5($pass));
 //        $bd->query('UPDATE `users` SET `pass`=?s WHERE `id`=?i',$new_pass,$_SESSION['id_restore']);
 //        $sms->send($_SESSION['phone_restore'],"Ваш новый пароль для входа на хочу-поесть: ".$pass);

 //        return json_encode(
 //            array(
 //                'status' => 'success_two'
 //            )
 //        );
 //    }


	// //Выход
	// public function logout (){
	// 	$bd = bd::getInstance();
	// 	global $config;


	// 	//Сохраняем ID города
 //        $city_id = $_SESSION['city']['id'];

	// 	//Чистим куки и сессию
	// 	session_unset();
	// 	session_destroy();
	// 	setcookie('user_id','',0,"/", ".".$config['domain']);
	// 	setcookie('hash','',0,"/", ".".$config['domain']);


	// 	//Изменяем город юзера
 //        header('Location: /changeCity/'.$city_id.'/');
 //        exit;

	// }

	// public function userRegistration($data){

	//     global $config;

	// 	require_once("app/models/client/client.php");
 //        $bd = bd::getInstance();
 //        $sms = sms::getInstance();
 //        $extra = extra::getInstance();
 //        $clients = client::getInstance();

 //        $name = htmlspecialchars(trim($data['name']));
	// 	$phone = htmlspecialchars(trim($data['phone']));
 //        $pass = htmlspecialchars(trim($data['pass']));
 //        $pass_second = htmlspecialchars(trim($data['pass_second']));
 //        $promo = htmlspecialchars(trim($data['promo']));

 //        if( empty($name) || empty($phone) || empty($pass) || empty($pass_second) || $pass != $pass_second)
 //            return 'Вы не зарагастрированы, введите данные еще раз.';

 //        $name = $extra->mb_ucfirst($name);

 //        if( $this->info() ) return 'Вы зарагастрированы.';

 //        $login = $extra->numberForText($phone);
 //        if( !$login )
 //            return 'Неверный номер телефона.';


 //        //Получаем ID пригласившего партнера
 //        $partner_id = 0;
 //        if( $promo ){
 //            $partner_id = (int)$bd->getOne('SELECT `id` FROM `partners` WHERE `promo` LIKE ?s LIMIT 1', '%"'.$promo.'"%');
 //            if( !$partner_id ) {
 //                return 'Введенный промокод не действителен!';
 //            }
 //        }

 //        $user_data = $this->registration($login, 'user',  md5(md5($pass)));
 //        if ( !$user_data['success']) return 'Вам необходимо авторизоваться. Ваш логин: '.$login;


 //        $array = array(
 //            'id' => $user_data['user_id'],
 //            'name' => $name,
 //            'phone' => $phone,
 //            'partner_id' => $partner_id,
 //            'add_time' => time()
 //        );
 //        if(!empty($_COOKIE["invite_id"])) $array['invite_id'] = $_COOKIE["invite_id"];
 //        $bd->query('INSERT INTO `clients` SET ?u',$array);

 //        //Проводим авторизацию клиента
 //        $this->login(array(
 //            'login' => $login,
 //            'pass' => $pass
 //        ));

 //        //Записываем данные в сесию, чтобы показать модальное окно
 //        //$_SESSION['hello-modal'] = true;


 //        //Отправляем клиенту СМС
 //        $text = "Спасибо, за регистрацию! Запишите данные для входа в личный кабинет:\n\rLogin: ".$login."\n\rПароль: ".$pass;
 //        $sms->send($phone,$text);

 //        //Добавление бонусов при регистрации 
 //       	$startBonus = $clients->startBonus(200);
 //       	if (!empty($startBonus)) {
 //       		$clients->addBonus($startBonus['summa'], $startBonus['comment']);
 //       	}

 //        return 'success';
 //    }


	// //Получить ссылку на кабинет пользователя
	// public function getCabinetLink (){
	// 	$this->info();

	// 	if( $this->data['access'] == 'restaurant' ){
	// 		return '/restaurant/';
	// 	}elseif( $this->data['access'] == 'franchise' ){
	// 		return '/franchise/';

	// 	}elseif( $this->data['access'] == 'user' ){
 //            return '/client/';

 //        }elseif( $this->data['access'] == 'admin' ){
 //            return '/admin-panel/';

 //        }elseif( $this->data['access'] == 'manager' ){
 //            return '/admin-panel/';

 //        }elseif( $this->data['access'] == 'partner' ){
 //            return '/partners/';
 //        }

	// 	return '/';
	// }

	// //Регистрация
	// public function registration ($login, $access, $pass){
	// 	$bd = bd::getInstance();

	// 	if ($bd->getOne('SELECT `login` FROM `users` WHERE `login`  LIKE ?s LIMIT 1', $login )) 
	// 		return ['success' => false];

	// 	$hash = md5(time().rand());
	// 	$array = array(
 //            'login' => $login,  
 //            'access' => $access,
 //            'pass' => $pass,
 //            'hash' => $hash
 //        );

 //        $bd->query('INSERT INTO `users` SET ?u',$array);
 //        $user_id = $bd->insertId();
	// 	return [
	// 		'success' => true,
	// 		'user_id' => $user_id
	// 	];
	// }

	// public function registrationUpdate ($id, $login, $pass){
	// 	$bd = bd::getInstance();

	// 	$hash = md5(time().rand());

	// 	$array = array(
 //            'login' => $login,
 //            'hash' => $hash
 //        );

 //        if (!empty($pass)) {
	// 		$array['pass'] = $pass;
	// 	}

 //        if ($bd->getOne('SELECT `login` FROM `users` WHERE `login` LIKE ?s AND `id` NOT IN (?i) LIMIT 1', $login, $id )) 
	// 		return ['success' => false];

 //        $bd->query('UPDATE `users` SET ?u WHERE `id`=?i',$array,$id);
	// 	return ['success' => true];
	// }

    //Получить город юзера
    public $city = false;
    public function getCity (){

        /*if( $this->city )
            return $this->city;*/

        if( $_SESSION['city'] ){
            $this->city = $_SESSION['city'];
            return $this->city;
        }

        $geo = geo::getInstance();

        $city = $geo->getCity();
        $_SESSION['city'] = $city;
        $this->city = $city;

        return $this->city;
    }

    //Изменить город юзера
    public function changeCity($id){
        $db = db::getInstance();
        $controller = controller::getInstance();

        $city = $db->getRow('SELECT * FROM `geo_city` WHERE `id`=?i', (int)$id );
        if( !$city )
            return false;

        //Если это Москва - записываем в куки, что мы хотим попасть в Москву

        global $config;
        $time_cookies = mktime(0,0,0,1,1,2030);

        if( $city['id'] == 5054 ):
            setcookie('changeMoscow', true, $time_cookies, "/", ".".$config['domain']);
            $_SESSION['changeMoscow'] = true;

            $_SESSION['city'] = $city;
            $http_referer = $controller->getAddress($_SERVER['HTTP_REFERER']);
            header('Location: '.$config['protocol'].'://'.$config['domain'].'/'.$http_referer.'?changeMoscow=true');
            exit;

        //Если не Москва
        else:
            setcookie('changeMoscow', false, 0, "/", ".".$config['domain']);
            $_SESSION['changeMoscow'] = false;
        endif;

        $_SESSION['city'] = $city;
        return true;
    }

 //    public function is_login($login, $id=false){
	// 	$bd = bd::getInstance();
	// 	$user_data = $bd->getRow('SELECT `id`, `login` FROM `users` WHERE `login`  LIKE ?s LIMIT 1', $login );
	// 	if (!empty($id)) {
	// 		if ((int)$user_data['id'] == (int)$id) {
	// 			return ['success' => false];
	// 		}
	// 	}
		
	// 	if (!empty($user_data))
	// 		return ['success' => true];

	// 	return ['success' => false];
	// }

}



?>