<?php

global $settings;
$settings = settings::getInstance();

class settings
{
	public $default_lang='ru';
    protected static $_instance;
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}


    public static function getInstance()
    {
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

	//Получить список языков
	public function langList(){
		$db = db::getInstance();
		return $db->getInd('sname','SELECT * FROM `lang`');
	}

	//Получаем настройки сайта
	public function get_site_options($name=false)
    {
		$db = db::getInstance();
		return $db->getInd('name','SELECT * FROM `site_option`');
	}

	public function update_mo($data)
    {
		$lang_sname=$data['sname'];
		$db = db::getInstance();
		$lang=$db->getRow('SELECT * FROM lang WHERE sname=?s',$lang_sname);
		if($lang){
			$path=$_SERVER['DOCUMENT_ROOT'].'/locale/'.$lang['locale'].'/LC_MESSAGES/';
			$mo_files=glob($path.'mess*.mo');
			$time=$lang['mod_time'];
			foreach ($mo_files as $mo){ //Если оставляем только оригинальный mo и последнюю
				$filename=basename($mo);
				if($filename==='mess.mo'){
					$time=filemtime($mo);
					copy($mo,$path.'mess_'.$time.'.mo');
					$db->query('UPDATE lang SET mod_time=?i WHERE sname=?s',$time,$lang_sname);
				}
				elseif ($filename==='mess_'.$time.'.mo') continue;
				else unlink($mo);
			}
			return 'mess_'.$time;
		}
		return false;
	}

	public function get_trans_domain($lang)
    {
		$path=$_SERVER['DOCUMENT_ROOT'].'/locale/'.$lang['locale'].'/LC_MESSAGES/';
		if(is_file($path.'mess_'.$lang['mod_time'])) return 'mess_'.$lang['mod_time'];
		else{
			if(!is_file($path.'mess.mo')) return false;
			$max=0;
			$mo_files=glob($path.'mess*.mo');
			foreach ($mo_files as $mo){
				$filename=basename($mo);
				preg_match('/mess_(.*?)\.mo/',$filename,$matches);
				if(isset($matches[1])){
					if($matches[1] > $max) $max=$matches[1];
				}
			}
			if($max==0){
				return $this->update_mo($lang);
			}
			else {
				$db = db::getInstance();
				$db->query('UPDATE lang SET mod_time=?i WHERE sname=?s',$max,$lang['sname']);
				return 'mess_'.$max;
			}
		}
	}

	//Получить все блоки для страницы
	public function get_page_blocks($data)
    {
		$db = db::getInstance();
		$blocks_tmp=$db->getAll('SELECT * FROM `htmlblock` WHERE page_name=?s AND lang=?s ',$data['page'],$data['lang']);
		if(!$blocks_tmp) return false;
		$blocks=[];
		foreach ($blocks_tmp as $block){
			$blocks[$block['name']]=$block['val'];
		}
		return $blocks;
	}

	//Получить содержимое определенного блока
	public function get_block_val($data)
    {
		$db = db::getInstance();
		return $db->getOne('SELECT val FROM `htmlblock` WHERE page_name=?s AND lang=?s ',$data['page'],$data['lang']);
	}


    public function setTranslate($locale, $domain)
    {
	    putenv("LANGUAGE=$locale");
	    setlocale(LC_ALL, $locale);
	    bindtextdomain($domain, "./locale");
	    textdomain($domain);
    }

	//Получить список форм
	public function formList()
    {
		return [ 'form_application' => ['name'=>'index-form.html','desc'=>'Полная главная форма'],'form_cat' => ['name'=>'cat-form.html','desc'=>'Основная форма для категорий'],'form_shinomontag' => ['name'=>'shinomontag-form.html','desc'=>'Форма шиномонтажа'],'form_avtourist' => ['name'=>'avtourist-form.html','desc'=>'Форма автоюриста'],'form_sokr' => ['name'=>'index-form-sokr.html','desc'=>'Сокращенная форма'] ];
	}


    // вернуть ид и емейл города по названию
    public function getCityByName($data)
    {
        $db = db::getInstance();
        $row= $db->getRow('SELECT id, city_email
        FROM `geo_city`
        WHERE name=?s',$data);
        return $row;
    }


    // получить данные о юзере если авторизирован
    public function authuser ()
    {
        $clid=$_SESSION['client']['id'];
        if(!$clid) return false;
        //5054
        $db = db::getInstance();
        return $db->getRow('SELECT client.*, geo_city.name as gorod
        FROM `client`
        INNER JOIN `geo_city` ON `client`.`city_id`=`geo_city`.`id`
        WHERE `client`.`id`=?i',$clid);
    }


    //добавить заявку в БД
    public function add_history($data=false)
    {
        $db = db::getInstance();
        if(!$data) return false;

        $k="id, ";
        $v="null, ";
        foreach($data as $key=>$val) {
            $k.="$key, ";
            $v.="'$val', ";
        }
        $k=substr($k,0,strlen($k)-2 );
        $v=substr($v,0,strlen($v)-2 );

        $sql="INSERT INTO history ($k) VALUE ($v); ";

        if ($sql) {
            $db = db::getInstance();
            $my_history= $db->query($sql);
            return true;
        }
        return false;

    }

    // получить данные о юзере если авторизирован
    public function myAuto()
    {

        $clid=$_SESSION['client']['id'];
        if(!$clid) return 'var myavto = {};
        ';

        $db = db::getInstance();
        $row= $db->getAll('SELECT id, autoBrand_id, autoModel_id, vin, year_issue
        FROM `cars_clients`
        WHERE `client_id`=?i',$clid);

        //масив моих авто для автоподставновки года и ВИНа , вывод в frontend\templates\general\blocks\index-form-sokr.html
        $jsvars='var myavto = {};
        ';
        foreach($row as $key=>$v) {
            if( $v['id'] ) {
                //$marka=$v['autoBrand_id'];
                $model=$v['autoModel_id'];
                if($model) {
                    $jsvars.="myavto[$model]={\n";
                    foreach($v as $v2=>$v3) {
                        if(!$v3) $v3='0';
                        if($v2=='vin') $v2_tmp=$v3;
                        if($v2=='year_issue') $v3_tmp=$v3;
                    }

                    $jsvars.="  vin:'$v2_tmp',\n";
                    $jsvars.="  year:'$v3_tmp'\n
                    };\n";

                }
            }
        }

    //return htmlspecialchars_decode($jsvars);
    return $jsvars;

    }

    //Получить марки
    public function autoBrands()
    {
        $db = db::getInstance();
        $clid=$_SESSION['client']['id'];

        if($clid) {
            $cl_cars = $db->getAll('SELECT  car_mark.*
            FROM `cars_clients`
            INNER JOIN `car_mark` ON `cars_clients`.`autoBrand_id`=`car_mark`.`id`
            WHERE `cars_clients`.`client_id` = ?i order by id',(int)$clid);

            //получаем список ИД добавленных клиентом авто
            foreach($cl_cars as $key=>$v)
                $cl_cars_list[]=$v['id'];
            //получаем список всех авто кроме клиентских
            $rows = $db->getInd('id','SELECT * FROM `car_mark`
            ORDER BY `name` ASC');
            foreach($rows as $k)
                if(in_array($k,$cl_cars_list )) unset($rows[$k]);
                
            $res= array_merge_recursive ($cl_cars, $rows );
            $res2=array();
            foreach($res as $key) //ID  в ключ
                $res2[$key['id']]=$key;
            
            return $res2;
        }
        
        return $db->getInd('id','SELECT * FROM `car_mark` ORDER BY `name` ASC');
    }

    //Получить модели
    public function autoModels()
    {
        $clid=$_SESSION['client']['id'];
        $db = db::getInstance();
        //если юзер атворизирован подтягиваем его авто
        if($clid) {
            $cl_cars = $db->getAll('SELECT  car_model.*, cars_clients.autoBrand_id as brand_id
            FROM `cars_clients`
            INNER JOIN `car_model` ON `cars_clients`.`autoModel_id`=`car_model`.`id`
            WHERE `cars_clients`.`client_id` = ?i order by id',(int)$clid);

            //получаем список ИД добавленных клиентом авто
            foreach($cl_cars as $key=>$v)
                $cl_cars_list[]=$v['id'];

            $rows = $db->getInd('id','SELECT * FROM `car_model` ORDER BY `name` ASC');
            //удаляем дубли
            foreach($rows as $k=>$v)
                if(in_array($v['id'],$cl_cars_list ))      unset($rows[$k]);
            $rows=array_merge_recursive ($cl_cars, $rows );
        } else {
            $rows = $db->getInd('id','SELECT * FROM `car_model` ORDER BY `name` ASC');
        }

        $array = array();
        foreach( $rows as $id => $row )
            $array[$row['brand_id']][$row['id']] = $row;


        return $array;

    }

    //Год выпуска
    public function autoYears()
    {

        $array = array();
        for( $i = 1970; $i <= date('Y',time()); $i++ )
            $array[] = $i;

        return $array;

    }

    //Получить весь список категорий и работ (вытащить всю таблиу cats)
    public function GetAllCategorys($lang)
    {
        $db = db::getInstance();
	    $rows = $db->getAll('SELECT c.`id`, c.`parent_id`, cl.`title` as category
            FROM `cats` c
            INNER JOIN `cats_lang` cl ON (c.id=cl.cat_id AND cl.lang=?s)
            ORDER BY type, title',$lang);
        //$rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workCategory"'),1);
        $res=array();
        foreach($rows as $key) {
            if($key['parent_id'])
                $res[$key['parent_id']][$key['id']]=$key;
            else $res[$key['id']]=$key;

        }


        return $res;
    }

    //Получить весь список категорий и работ (вытащить всю таблиу cats) //для азказов
    public function GetAllCategorys_orders($lang)
    {
        $db = db::getInstance();
	    $rows = $db->getAll('SELECT c.`id`, c.`parent_id`, cl.`title` as title
            FROM `cats` c
            INNER JOIN `cats_lang` cl ON (c.id=cl.cat_id AND cl.lang=?s)
            ORDER BY type, title',$lang);
        //$rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workCategory"'),1);
        $res=array();
        foreach($rows as $key) {
            if($key['parent_id']) {
                $res[$key['parent_id']]['child'][$key['id']]=$key;
                $res[$key['parent_id']]['child'][$key['id']]['name']=$key['title'];
            }
            else $res[$key['id']]=$key;

        }

        return $res;
    }



	//Получить ссылку на вид работ
	public function getWorkLink($work_address)
    {
		$db = db::getInstance();
		$parent_address=$db->getOne('SELECT p.address FROM `cats` c INNER JOIN cats p ON (c.parent_id=p.id) WHERE c.address=?s',$work_address);
		if($parent_address) $work_address=$parent_address.'/'.$work_address;
		return $work_address;
	}




  //Получить список категорий
    public function workMainCategory($lang)
    {
        $db = db::getInstance();
	    $rows = $db->getAll('SELECT c.id,c.address,c.parent_id,c.img_icon,c.type,cl.title FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s) WHERE ((c.type=1 AND c.parent_id=0 AND c.img_icon IS NOT NULL) OR c.address="gbo" )  ORDER BY type,title',$lang);
        //$rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workCategory"'),1);
        return $rows;
       /* $array = array(
            1 => 'Диагностика',
            2 => 'Кузовные работы',
            3 => 'Подвеска',
            4 => 'Стёкла, фары',
            5 => 'Тормозная система',
            6 => 'Электрика',
            7 => 'Дополнительное оборудование',
            8 => 'Масло, жидкости, фильтры',
            9 => 'Полировка, уход',
            10 => 'Техническое обслуживание',
            11 => 'Трансмиссия',
            12 => 'Защитные плёнки',
            13 => 'Мойка, химчистка',
            14 => 'Сложный агрегатный ремонт',
            15 => 'Тонировка',
            16 => 'Шиномонтаж',
            17 => 'Услуги с выездом',
            18 => 'Автоюрист',
            19 => 'Замена АКБ',
            20 => 'Покупка резины',
            0 => 'Другое'
        );
        return $array;*/

    }

    //Получить список видов работ
    public function workType()
    {

        $db = db::getInstance();
        $rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workType"'),1);
        return $rows;

        $array = array(
            1 => array(
                1 => 'Диагностика АКПП',
                2 => 'Диагностика кондиционера',
                3 => 'Диагностика подвески',
                4 => 'Диагностика тормозной системы',
                5 => 'Заправка кондиционера',
                6 => 'Комплексная диагностика',
                7 => 'Компьютерная диагностика',
                0 => 'Другое'
            ),
            2 => array(
                8 => 'Дефектовка',
                9 => 'Замена бампера',
                10 => 'Замена двери',
                11 => 'Замена капота',
                12 => 'Замена крыла',
                13 => 'Замена крыши',
                14 => 'Замена крышки багажника',
                15 => 'Замена порога',
                16 => 'Окраска накладки наружного зеркала',
                17 => 'Покраска бампера',
                18 => 'Покраска двери',
                19 => 'Покраска капота',
                20 => 'Покраска крыла',
                21 => 'Покраска крыши',
                22 => 'Покраска крышки багажника',
                23 => 'Покраска порога',
                24 => 'Полная покраска (наружная)',
                0 => 'Другое'
            ),
            3 => array(
                25 => 'Замена амортизаторов',
                26 => 'Замена втулок стабилизатора',
                27 => 'Замена заднего рычага',
                28 => 'Замена насоса ГУР',
                29 => 'Замена переднего рычага',
                30 => 'Замена рулевой тяги/наконечника',
                31 => 'Замена сайлентблока',
                32 => 'Замена стоек стабилизатора',
                33 => 'Замена ступицы в сборе',
                34 => 'Замена ступичного подшипника',
                35 => 'Замена шаровой опоры',
                36 => 'Проверка сход/развала',
                37 => 'Развал-схождение',
                38 => 'Ремонт пневмостойки',
                39 => 'Ремонт рулевой рейки',
                0 => 'Другое'
            ),
            4 => array(
                40 => 'Бронирование фар',
                41 => 'Замена бокового стекла',
                42 => 'Замена бокового стекла с выездом',
                43 => 'Замена заднего стекла',
                44 => 'Замена ксеноновых ламп',
                45 => 'Замена лобового стекла',
                46 => 'Полировка фар',
                47 => 'Ремонт стекла',
                0 => 'Другое'
            ),
            5 => array(
                48 => 'Замена блока ABS, EBD, ESP',
                49 => 'Замена задних тормозных барабанов',
                50 => 'Замена колодок стояночного тормоза',
                51 => 'Замена тормозных дисков',
                52 => 'Замена тормозных колодок',
                53 => 'Замена тормозных шлангов',
                54 => 'Регулировка стояночного тормоза',
                55 => 'Регулировка тросов стояночного тормоза центральным натяжением',
                0 => 'Другое'
            ),
            6 => array(
                56 => 'Замена генератора',
                57 => 'Замена датчика ABS',
                58 => 'Замена датчика положения коленвала',
                59 => 'Замена расходомера',
                60 => 'Замена стартера',
                61 => 'Ремонт генератора',
                62 => 'Ремонт стартера',
                0 => 'Другое'
            ),
            7 => array(
                63 => 'Замена катализатора',
                64 => 'Замена катализатора с "обманкой"',
                65 => 'Установка камеры заднего вида',
                66 => 'Установка парктроника',
                67 => 'Установка сигнализации',
                0 => 'Другое'
            ),
            8 => array(
                68 => 'Замена антифриза',
                69 => 'Замена воздушного фильтра',
                70 => 'Замена жидкости ГУР',
                71 => 'Замена масла',
                72 => 'Замена масла АКПП',
                73 => 'Замена масла МКПП',
                74 => 'Замена салонного фильтра',
                75 => 'Замена свечей',
                76 => 'Замена топливного фильтра',
                77 => 'Замена тормозной жидкости',
                0 => 'Другое'
            ),
            9 => array(
                78 => 'Абразивная полировка кузова',
                79 => 'Антикоррозийная обработка днища',
                80 => 'Дезинфекция кондиционера',
                81 => 'Мягкая восстановительная полировка кузова',
                82 => 'Полировка детали',
                83 => 'Полировка кузова жидким стеклом',
                84 => 'Полировка фар',
                85 => 'Предпродажная подготовка',
                0 => 'Другое'
            ),
            10 => array(
                86 => 'Базовое ТО',
                87 => 'Замена помпы',
                88 => 'Замена ремней и роликов вспомогательных механизмов',
                89 => 'Замена ремня ГРМ',
                90 => 'Замена цепи ГРМ',
                91 => 'Полное ТО',
                0 => 'Другое'
            ),
            11 => array(
                92 => 'Адаптация механизма выжима сцепления роботизированной трансмиссии',
                93 => 'Замена ШРУСа',
                94 => 'Замена пыльника шруса',
                95 => 'Замена сцепления',
                96 => 'Ремонт коробки передач',
                0 => 'Другое'
            ),
            12 => array(
                93 => 'Защитные пленки',
                0 => 'Другое'
            ),
            13 => array(
                94 => 'Комплексная мойка',
                95 => 'Стандартная мойка',
                96 => 'Устранение запахов',
                97 => 'Химчистка салона',
                98 => 'Экспресс-мойка',
                0 => 'Другое'
            ),
            14 => array(
                99 => 'Ремонт двигателя',
                100 => 'Ремонт коробки передач',
                101 => 'Ремонт раздаточной коробки',
                102 => 'Сложный ремонт электрики',
                0 => 'Другое'
            ),
            15 => array(
                103 => 'Атермальная пленка',
                104 => 'Снятие тонировочной пленки',
                105 => 'Тонировка автомобиля',
                0 => 'Другое'
            ),
            16 => array(
                106 => 'Комплексный шиномонтаж',
                107 => 'Съем-установка колес',
                0 => 'Другое'
            ),
            17 => array(
                108 => 'Услуги с выездом',
                0 => 'Другое'
            ),
            18 => array(
                109 => 'Автоюрист',
                0 => 'Другое'
            ),
            19 => array(
                110 => 'Замена АКБ',
                0 => 'Другое'
            ),
            20 => array(
                111 => 'Покупка резины',
                0 => 'Другое'
            )
        );
        return $array;

    }

    //Получить список категорий (для форм)
    public function workMainCategory2($lang)
    {
        $db = db::getInstance();
	    $rows = $db->getAll('SELECT c.id,c.address,c.parent_id,c.img_icon,c.type,cl.title FROM `cats` c INNER JOIN cats_lang cl ON (c.id=cl.cat_id AND cl.lang=?s) WHERE ((c.type=1 AND c.parent_id=0 AND c.img_icon IS NOT NULL) OR c.address="gbo" )  ORDER BY type,title',$lang);
        //$rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workCategory"'),1);
        return $rows;

    }

    //Получить список видов работ для форм
    public function workType2()
    {

        $db = db::getInstance();
        //$rows = json_decode($db->getOne('SELECT `value` FROM `settings` WHERE `name`="workType"'),1);
        $rows = $db->getAll('SELECT `cat_id`, `title` FROM `cats_lang` WHERE lang="ru"') ;
        return $rows;

    }

}