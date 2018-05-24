<?php

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Если запрос послан с xmlhttprequest, то есть это ajax запрос
		$database = $api->get('/stocks/limm/',$this->GET[0]);

		$database = timesewe($database);
		echo json_encode($database);	
    exit;
} else {

	if (!empty($this->GET[0])) {
		$database = $api->get('/stocks/', $this->GET[0]);
		$template = 'acia.html';
	} else { 
		$database = $api->get('/stocks/limm/',0);
		$database = timesewe($database);
	}
	$vars['rows'] = $database;
}



function timesewe ($database){
	foreach ($database as &$values) {
		$i = rand(13, 590);
		$timeits = time();
		$timeunix = $values['time'];
		$ostatoktime = $timeunix - $timeits - $i;
		$ostatoktime = (int)$ostatoktime;
		if ( $ostatoktime <= 0) {
			$arryvalues = ['h'=>0,'m'=>0,'s'=>0,];
			$values['time'] = $arryvalues;
			return $database;
		}
		 
		 $chasi = floor($ostatoktime/3600);
		 $ostatoktime -= $chasi * 3600;
		 $arryvalues['h'] = $chasi;
		 $minutes = floor($ostatoktime/60);
		 $ostatoktime -= $minutes * 60;
		 $arryvalues['m'] = $minutes;
		 $arryvalues['s'] = $ostatoktime;
		$values['time'] = $arryvalues;
	}
	return $database;
}
