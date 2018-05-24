<?php

    $vars = array(
        'brands' => $settings->autoBrands(),
        'models' => $settings->autoModels(),
        'autoYears' => $settings->autoYears(),
        'workCategory' => $settings->workMainCategory($config['lang']),
        'workType' => $settings->worktype()
    );

?>