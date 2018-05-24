<?php

//Получить историю клиент
$db = db::getInstance();
$id_client  =$_SESSION['client']['id'];

$histories = $db->getAll('SELECT * FROM history WHERE id_client = ?i', $id_client);

$vars['histories'] = $histories;
$template = 'history.html';