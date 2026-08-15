<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('pajak', ['filter' => 'auth', 'namespace' => 'App\Modules\Pajak\Controllers'], function($routes){
	$routes->get('/', 'Pajak::index', ['filter' => 'permit:viewPajak']);
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Pajak\Controllers\Api'], function($routes){
    $routes->get('pajak', 'Pajak::index', ['filter' => 'permit:viewPajak']);
	$routes->get('pajak/saldo', 'Pajak::saldo', ['filter' => 'permit:viewPajak']);
	$routes->get('pajak/(:segment)', 'Pajak::show/$1', ['filter' => 'permit:viewPajak']);
	$routes->post('pajak/save', 'Pajak::create', ['filter' => 'permit:createPajak']);
	$routes->put('pajak/update/(:segment)', 'Pajak::update/$1', ['filter' => 'permit:updatePajak']);
	$routes->delete('pajak/delete/(:segment)', 'Pajak::delete/$1', ['filter' => 'permit:deletePajak']);
});