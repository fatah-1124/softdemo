<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('settings', ['filter' => 'auth', 'namespace' => 'App\Modules\Setting\Controllers'], function($routes){
	$routes->get('/', 'Setting::index', ['filter' => 'permit:viewSetting']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Setting\Controllers\Api'], function($routes){
    $routes->get('setting', 'Setting::index', ['filter' => 'permit:viewSetting']);
	$routes->put('setting/update/(:segment)', 'Setting::update/$1', ['filter' => 'permit:updateSetting']);
	$routes->post('setting/upload', 'Setting::upload', ['filter' => 'permit:updateSetting']);
});