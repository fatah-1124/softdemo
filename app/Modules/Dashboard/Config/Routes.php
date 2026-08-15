<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dashboard', ['filter' => 'auth', 'namespace' => 'App\Modules\Dashboard\Controllers'], function($routes){
	$routes->get('/', 'Dashboard::index', ['filter' => 'permit:viewDashboard']);
});