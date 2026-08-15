<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('biaya', ['filter' => 'auth', 'namespace' => 'App\Modules\Biaya\Controllers'], function($routes){
	$routes->get('/', 'Biaya::index', ['filter' => 'permit:viewBiaya']);
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Biaya\Controllers\Api'], function($routes){
    $routes->get('biaya', 'Biaya::index', ['filter' => 'permit:viewBiaya']);
	$routes->get('biaya/(:segment)', 'Biaya::show/$1', ['filter' => 'permit:viewBiaya']);
	$routes->post('biaya/save', 'Biaya::create', ['filter' => 'permit:createBiaya']);
	$routes->put('biaya/update/(:segment)', 'Biaya::update/$1', ['filter' => 'permit:updateBiaya']);
	$routes->delete('biaya/delete/(:segment)', 'Biaya::delete/$1', ['filter' => 'permit:deleteBiaya']);
});