<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('statistik', ['filter' => 'auth', 'namespace' => 'App\Modules\Statistik\Controllers'], function($routes){
	$routes->add('/', 'Statistik::index', ['filter' => 'permit:viewStatistik']);
});
