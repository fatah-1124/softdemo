<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('log', ['filter' => 'auth', 'namespace' => 'App\Modules\Log\Controllers'], function($routes){
	//$routes->get('/', 'Log::index');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Log\Controllers\Api'], function($routes){
	$routes->get('log', 'Log::index');
	$routes->get('log/(:segment)', 'Log::show/$1');
	$routes->get('loginlog', 'LoginLog::index');
	$routes->get('loginlog/last10', 'LoginLog::last10');
	$routes->get('loginlog/(:segment)', 'LoginLog::show/$1');
});