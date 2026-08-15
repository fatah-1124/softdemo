<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('piutang', ['filter' => 'auth', 'namespace' => 'App\Modules\Piutang\Controllers'], function($routes){
	$routes->get('/', 'Piutang::index', ['filter' => 'permit:viewPiutang']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Piutang\Controllers\Api'], function($routes){
    $routes->get('piutang', 'Piutang::index', ['filter' => 'permit:viewPiutang']);
	$routes->get('piutang/total', 'Piutang::total', ['filter' => 'permit:viewPiutang']);
	$routes->get('piutang/(:segment)', 'Piutang::show/$1', ['filter' => 'permit:viewPiutang']);
	$routes->post('piutang/save', 'Piutang::create', ['filter' => 'permit:createPiutang']);
	$routes->put('piutang/update/(:segment)', 'Piutang::update/$1', ['filter' => 'permit:updatePiutang']);
	$routes->delete('piutang/delete/(:segment)', 'Piutang::delete/$1', ['filter' => 'permit:deletePiutang']);
	$routes->delete('piutang/bayar/delete/(:segment)', 'Piutang::delete2/$1');
	$routes->get('find_piutang/(:segment)', 'Piutang::findPiutang/$1');
});