<?php
    $config = array(
        'debug' => 0,
        'db_server' => '127.0.0.1',
        'db_user' => '',
        'db_password' => '',
        'db_name' => '',
        //'domain' => 'в-автосервис.рф',
        //'protocol' => 'https',
		    'domain' => 'vavtoservis.geleos.net',
		    'protocol' => 'http',
        'email_admin' => 'webmaster@mfucentre.ru',
        'redirect' => 1,
        'templates_dir' => array(
            'frontend/templates',
            'frontend/templates/general',
            'frontend/templates/office',
            'frontend/templates/admin'
        ),
        'admin_login' => 'admin',
        'admin_pass' => '123456',
        'open_api' => false
    );
?>