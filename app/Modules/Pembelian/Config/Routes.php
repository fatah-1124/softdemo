<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('pembelian', ['filter' => 'auth', 'namespace' => 'App\Modules\Pembelian\Controllers'], function($routes){
	$routes->get('/', 'Pembelian::index', ['filter' => 'permit:viewPembelian']);
	$routes->get('baru', 'Pembelian::add', ['filter' => 'permit:viewPembelian']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Pembelian\Controllers\Api'], function($routes){
    $routes->get('pembelian', 'Pembelian::index', ['filter' => 'permit:viewPembelian']);
	$routes->get('pembelian/(:segment)', 'Pembelian::show/$1', ['filter' => 'permit:viewPembelian']);
	$routes->post('pembelian/save', 'Pembelian::create', ['filter' => 'permit:createPembelian']);
	$routes->put('pembelian/update/(:segment)', 'Pembelian::update/$1', ['filter' => 'permit:updatePembelian']);
	$routes->delete('pembelian/delete/(:segment)', 'Pembelian::delete/$1', ['filter' => 'permit:deletePembelian']);
	$routes->delete('pembelian/reset', 'Pembelian::truncate');
	$routes->get('pembelian/item/(:segment)', 'Pembelian::item/$1', ['filter' => 'permit:viewPembelian']);
});