<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $vars['avtoservices'] =  $api->get('/avtoservis/');
}