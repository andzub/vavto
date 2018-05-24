<?php
$user->changeCity($this->GET[0]);
header('location:' . $_SERVER['HTTP_REFERER']);
exit;


