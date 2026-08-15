<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Keranjang\Controllers\Api'], function($routes){
    $routes->get('keranjang', 'Keranjang::index');
	$routes->get('keranjang/beli', 'Keranjang::beli');
	$routes->get('keranjang/(:segment)', 'Keranjang::show/$1');
	$routes->get('keranjang/beli/(:segment)', 'Keranjang::show2/$1');
	$routes->post('keranjang/save', 'Keranjang::create');
	$routes->post('keranjang/beli/save', 'Keranjang::create2');
	$routes->put('keranjang/update/(:segment)', 'Keranjang::update/$1');
	$routes->put('keranjang/beli/update/(:segment)', 'Keranjang::update2/$1');
	$routes->delete('keranjang/delete/(:segment)', 'Keranjang::delete/$1');
	$routes->delete('keranjang/beli/delete/(:segment)', 'Keranjang::delete2/$1');
	$routes->delete('keranjang/reset', 'Keranjang::truncate');
	$routes->delete('keranjang/beli/reset', 'Keranjang::truncate2');
});