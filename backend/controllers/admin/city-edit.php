<?php

    $vars = array(
        'data' => $api->get('/cities/city/', $this->GET),
        'city' => $this->GET[0],
    );

?>
