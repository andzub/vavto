<?php

    //Пароль
    global $config;
    if ( !isset($_SERVER['PHP_AUTH_USER'] )) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Ошибка доступа!';
        exit;
    } else {

        if( ($_SERVER['PHP_AUTH_USER'] != $config['admin_login'] || $_SERVER['PHP_AUTH_PW'] != $config['admin_pass']) && $_COOKIE['admin_auth'] != md5($config['admin_login'].$config['admin_pass']) ){
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Ошибка доступа!';
            exit;
        }else{

            $time_cookies = mktime(0,0,0,1,1,2030);
            setcookie('admin_auth', md5($config['admin_login'].$config['admin_pass']), $time_cookies, "/", ".".$config['domain']);

        }

    }


    if( $_POST ){

        $data = $_POST;
        $data = explode("\n",$data['category']);

        $workCategory = array();
        $workType = array();

        $workCategory_id = 0;
        $workType_id = 0;
        foreach( $data as $key => $row ){

            if( mb_strlen(trim($row)) < 1 )
                continue;

            if( mb_substr($row,0,1) == '-' ){

                $workCategory_id++;
                $workCategory[$workCategory_id] = trim(mb_substr($row,1));
                $workType_id = 0;

            }else{

                $workType_id++;
                $workType[$workCategory_id][$workType_id] = trim($row);

            }

        }

        $workCategory[0] = 'Другое';
        foreach( $workType as $key => $row ){
            $workType[$key][0] = 'Другое';
        }

        $db->query('UPDATE `settings` SET `value`=?s WHERE `name`="workCategory"', json_encode($workCategory) );
        $db->query('UPDATE `settings` SET `value`=?s WHERE `name`="workType"', json_encode($workType) );

        header('Location: /admin/category/success/');
        exit;

    }


    $workCategory = $settings->workCategory();
    $workType = $settings->workType();

    $category = "";
    foreach( $workCategory as $key => $name ){

        if( $name == 'Другое' )
            continue;

        if( $category )
            $category .= "\n";

        $category .= '- '.$name;

        if( is_array($workType[$key]) ) {
            foreach ($workType[$key] as $type_key => $type_name) {

                if( $type_name == 'Другое' )
                    continue;

                $category .= "\n\t" . $type_name;

            }
        }

        $category .= "\n";

    }


    $vars = array(
        'category' => $category,
        'success' => $this->GET[0]
    );

?>
