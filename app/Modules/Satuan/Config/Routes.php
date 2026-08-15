<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('satuan', ['filter' => 'auth', 'namespace' => 'App\Modules\Satuan\Controllers'], function($routes){
	$routes->get('/', 'Satuan::index');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Satuan\Controllers\Api'], function($routes){
	$routes->get('satuan', 'Satuan::index');
	$routes->get('satuan/(:segment)', 'Satuan::show/$1');
	$routes->post('satuan/save', 'Satuan::create');
	$routes->put('satuan/update/(:segment)', 'Satuan::update/$1');
	$routes->delete('satuan/delete/(:segment)', 'Satuan::delete/$1');
	$routes->get('satuan/where/(:segment)', 'Satuan::getSatuan/$1');
});