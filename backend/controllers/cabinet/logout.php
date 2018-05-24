<?php
if (isset($_SESSION['client']['id'])) {
	$_SESSION['client'] = array();
	
	header('location:' . $_SESSION['langlink'].'/');
    exit;

}