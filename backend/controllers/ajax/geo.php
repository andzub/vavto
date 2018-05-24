<?php

    $method = $this->GET[0];

    if( $method == 'getAllCity' ){
        $res= $geo->getAllCity($_POST['lang'],$_POST['q']);
        var_dump($res);

    }elseif( $method == 'getDistricts' || $method == 'getdistricts' ){
        echo $geo->getDistricts($_POST['city_id']);

    }
    exit;
