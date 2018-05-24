<?php
if ($this->GET[0] == 'model' ) {
	$model = $api->get('/cars/model/',$_POST);
	echo json_encode($model);
    exit;
}

//список моделей + клиентские в начале списка
if ($this->GET[0] == 'modelcl' ) {
    $model = $api->get('/cars/modelcl/',$_POST);
    echo json_encode($model);
    exit;
}
if ($this->GET[0] == 'brend' ) {
	$brend = $api->get('/cars/brend/',$_POST['q']);
	echo json_encode($brend);
    exit;
}

//список авто для клиента с учетом его добавленных
if ($this->GET[0] == 'brendcl' ) {
    $brend = $api->get('/cars/brendcl/',$_POST['q']);
    echo json_encode($brend);
    exit;
}