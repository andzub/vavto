<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(isset($_POST['method'])){

		$method = $_POST['method'];
		$uri = $_POST['uri'];

		if(method_exists($api, $method))
			echo json_encode($api->{$method}($uri, $_POST));

	}

}

exit;