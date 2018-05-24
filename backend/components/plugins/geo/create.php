<?php

    /*
     *
     *     ФОРМИРУЕМ ДАННЫЕ ГЕО В НУЖНОМ ВИДЕ С БАЗЫ ФИАС
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


    //Выбираем регионы
    $regions = $bd->getAll('SELECT `regioncode`,`shortname`,`offname` FROM `d_fias_addrobj` WHERE `aolevel`=1 AND `shortname`!="г" AND `actstatus`=1');
    if( count($regions) < 1 ){
        die('Произошла ошибка!');
    }


    //Очищаем таблицы
    $bd->query('TRUNCATE geo_regions');
    $bd->query('TRUNCATE geo_city');


    //Формируем регионы
    $sql = $regions;
    $sql_insert = array();
    foreach( $sql as $row ){

        $short_first = '';
        $short_end = '';

        if( $row['shortname'] == 'обл' ) {
            $short_end = ' область';

        }elseif( $row['shortname'] == 'Респ' ){
            $short_first = 'Республика ';

        }elseif( $row['shortname'] == 'Аобл' ){
            $short_end = ' автономная область';

        }else{
            $short_end = ' '.$row['shortname'];
        }

        $sql_insert[] = $bd->parse('(?i,?s)', (int)$row['regioncode'], $short_first.$row['offname'].$short_end);
    }
    $bd->query('INSERT INTO `geo_regions` (`id`,`name`) VALUES ?p', implode(',',$sql_insert) );


    //Формируем города
    $sql = $bd->getAll('SELECT `regioncode`,`shortname`,`offname` FROM `d_fias_addrobj` WHERE ( `aolevel`=4 OR (`aolevel`=1 AND `shortname`="г") ) AND `actstatus`=1 ORDER BY `regioncode` ASC');
    $sql_insert = array();
    foreach( $sql as $row ){
        $sql_insert[] = $bd->parse('(?i,?s)', (int)$row['regioncode'], $row['offname']);
    }
    $bd->query('INSERT INTO `geo_city` (`region_id`,`name`) VALUES ?p', implode(',',$sql_insert) );


    //Формируем районы для городов


    echo 'success';

?>