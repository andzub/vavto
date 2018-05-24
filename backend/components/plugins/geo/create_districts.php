<?php

    /*
     *
     *     Формируем районы
     *
     */

    header("Content-Type: text/html; charset=UTF-8");
    define("SECRET_Z*#(1219X*!sZZS", TRUE);

    require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/app/models/bd.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/app/models/user.php");

    $user->info();
    if( $user->data['access'] != 'admin' ){
        die('Нет доступа!');
    }


    die('Скрипт выключен.');


    require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/simple_html_dom/simple_html_dom.php');
    $html = new simple_html_dom();


    $html = str_get_html('
    <select class="js-regions-region"> <option value="">Выбрать регион</option>
          <option value="637640">Москва</option>
          <option value="637680">Московская область</option>
          <option value="653240">Санкт-Петербург</option>
          <option value="636370">Ленинградская область</option>
          <option value="645530">Адыгея</option>
          <option value="621590">Алтайский край</option>
          <option value="622470">Амурская область</option>
          <option value="622650">Архангельская область</option>
          <option value="623110">Астраханская область</option>
          <option value="645790">Башкортостан</option>
          <option value="623410">Белгородская область</option>
          <option value="623840">Брянская область</option>
          <option value="623845">Бурятия</option>
          <option value="624300">Владимирская область</option>
          <option value="624770">Волгоградская область</option>
          <option value="625330">Вологодская область</option>
          <option value="625670">Воронежская область</option>
          <option value="646710">Дагестан</option>
          <option value="626470">Еврейская АО</option>
          <option value="661460">Забайкальский край</option>
          <option value="628450">Ивановская область</option>
          <option value="628455">Ингушетия</option>
          <option value="628780">Иркутская область</option>
          <option value="629430">Кабардино-Балкария</option>
          <option value="629990">Калининградская область</option>
          <option value="629995">Калмыкия</option>
          <option value="630270">Калужская область</option>
          <option value="630660">Камчатский край</option>
          <option value="630750">Карачаево-Черкесия</option>
          <option value="648070">Карелия</option>
          <option value="631080">Кемеровская область</option>
          <option value="631730">Кировская область</option>
          <option value="648340">Коми</option>
          <option value="632390">Костромская область</option>
          <option value="632660">Краснодарский край</option>
          <option value="634930">Красноярский край</option>
          <option value="621550">Крым</option>
          <option value="635730">Курганская область</option>
          <option value="636030">Курская область</option>
          <option value="637260">Липецкая область</option>
          <option value="637530">Магаданская область</option>
          <option value="648730">Марий Эл</option>
          <option value="648960">Мордовия</option>
          <option value="640000">Мурманская область</option>
          <option value="640001">Ненецкий АО</option>
          <option value="640310">Нижегородская область</option>
          <option value="641240">Новгородская область</option>
          <option value="641470">Новосибирская область</option>
          <option value="642020">Омская область</option>
          <option value="642480">Оренбургская область</option>
          <option value="643030">Орловская область</option>
          <option value="643250">Пензенская область</option>
          <option value="643700">Пермский край</option>
          <option value="644490">Приморский край</option>
          <option value="645260">Псковская область</option>
          <option value="662811">Республика Алтай</option>
          <option value="651110">Ростовская область</option>
          <option value="652220">Рязанская область</option>
          <option value="652560">Самарская область</option>
          <option value="653420">Саратовская область</option>
          <option value="653430">Сахалинская область</option>
          <option value="649330">Саха (Якутия)</option>
          <option value="653700">Свердловская область</option>
          <option value="649820">Северная Осетия</option>
          <option value="654860">Смоленская область</option>
          <option value="655190">Ставропольский край</option>
          <option value="656520">Тамбовская область</option>
          <option value="650130">Татарстан</option>
          <option value="656890">Тверская область</option>
          <option value="657310">Томская область</option>
          <option value="657610">Тульская область</option>
          <option value="650690">Тыва</option>
          <option value="658170">Тюменская область</option>
          <option value="659200">Удмуртия</option>
          <option value="659540">Ульяновская область</option>
          <option value="659930">Хабаровский край</option>
          <option value="650890">Хакасия</option>
          <option value="660300">Ханты-Мансийский АО</option>
          <option value="660710">Челябинская область</option>
          <option value="660711">Чеченская Республика</option>
          <option value="662000">Чувашия</option>
          <option value="662280">Чукотский АО</option>
          <option value="662330">Ямало-Ненецкий АО</option>
          <option value="662530">Ярославская область</option>
          </select>
    ');

    $regions = $html->find('select option');
    foreach( $regions as $region ){
        $region_id = (int)$region->value;
        if( $region_id < 1 )
            continue;

        $response = request('https://www.avito.ru/js/locations?json=true&id='.$region_id);
        $response = json_decode($response,1);

        foreach( $response as $city ){

            $our_city = $bd->getRow('SELECT * FROM `geo_city` WHERE `name`=?s',$city['name']);
            if( !$our_city ){
                echo "Нет города: ".$city['name'];
                echo "<BR>";
                continue;
            }


            $districts = request('https://www.avito.ru/js/directions?locid='.$city['id'].'&catid=4');
            $districts = json_decode($districts,1);

            if( !is_array($districts) )
                continue;

            if( $districts['metro'] ){
                $districts = $districts['metro'];
            }else{
                $districts = $districts['district'];
            }

            if( !is_array($districts) || count($districts) < 1 )
                continue;

            $sql = array();
            foreach( $districts as $district ){
                $sql[] = $bd->parse('(?i,?s)',$our_city['id'],$district['name']);
            }
            $bd->query("INSERT INTO `eda`.`geo_districts` (`city_id`, `name`) VALUES ?p", implode(',',$sql));

        }


    }



    function request($url){
        sleep(0.4);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    echo 'success';

?>