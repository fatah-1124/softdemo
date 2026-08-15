<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('hutang', ['filter' => 'auth', 'namespace' => 'App\Modules\Hutang\Controllers'], function($routes){
	$routes->get('/', 'Hutang::index', ['filter' => 'permit:viewHutang']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Hutang\Controllers\Api'], function($routes){
    $routes->get('hutang', 'Hutang::index', ['filter' => 'permit:viewHutang']);
	//$routes->get('hutang/total', 'Hutang::total');
	$routes->get('hutang/(:segment)', 'Hutang::show/$1', ['filter' => 'permit:viewHutang']);
	$routes->post('hutang/save', 'Hutang::create', ['filter' => 'permit:createHutang']);
	$routes->put('hutang/update/(:segment)', 'Hutang::update/$1', ['filter' => 'permit:updateHutang']);
	$routes->delete('hutang/delete/(:segment)', 'Hutang::delete/$1', ['filter' => 'permit:deleteHutang']);
	$routes->delete('hutang/bayar/delete/(:segment)', 'Hutang::delete2/$1');
});