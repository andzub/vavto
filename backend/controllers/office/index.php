<?php

    /*$rows = $db->getAll('SELECT * FROM users');
    print_r($rows);
    echo 'Главная!';*/

    $_POST = array(
        'id' => 123
    );



    $a = $api->get('/user/info/?id=530&access=admin');
    $b = $api->get('/user/?id=530');
    $c = $api->get('/user/info/',$_POST);
    $d = $api->get('/user/',500);
    $e = $api->get('/exit/',500);

    print_r($a);
    print_r($b);
    print_r($c);
    print_r($d);
    print_r($e);

    //$api->post('/user/',$_POST);
    //$api->put('/user/?id=1000',$_POST);
    //$api->delete('/user/?id=2000');

    //$template = 'general/books.html';
    $vars = array(
        'a' => 123
    );

?>