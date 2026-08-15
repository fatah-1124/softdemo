<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('kontak', ['filter' => 'auth', 'namespace' => 'App\Modules\Kontak\Controllers'], function($routes){
	$routes->get('/', 'Kontak::index', ['filter' => 'permit:viewKontak']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Kontak\Controllers\Api'], function($routes){
    $routes->get('kontak', 'Kontak::index', ['filter' => 'permit:viewKontak']);
	$routes->get('kontak/pelanggan', 'Kontak::pelanggan', ['filter' => 'permit:viewKontak']);
	$routes->get('kontak/vendor', 'Kontak::vendor', ['filter' => 'permit:viewKontak']);
	$routes->get('kontak/(:segment)', 'Kontak::show/$1', ['filter' => 'permit:viewKontak']);
	$routes->post('kontak/save', 'Kontak::create', ['filter' => 'permit:createKontak']);
	$routes->put('kontak/update/(:segment)', 'Kontak::update/$1', ['filter' => 'permit:updateKontak']);
	$routes->delete('kontak/delete/(:segment)', 'Kontak::delete/$1', ['filter' => 'permit:deleteKontak']);
});