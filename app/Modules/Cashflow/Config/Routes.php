<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('cashflow', ['filter' => 'auth', 'namespace' => 'App\Modules\Cashflow\Controllers'], function($routes){
	$routes->get('/', 'Cashflow::index', ['filter' => 'permit:viewCashflow']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Cashflow\Controllers\Api'], function($routes){
    $routes->get('cashflow', 'Cashflow::index', ['filter' => 'permit:viewCashflow']);
	$routes->get('cashflow/saldo', 'Cashflow::saldo', ['filter' => 'permit:viewCashflow']);
	$routes->get('cashflow/(:segment)', 'Cashflow::show/$1', ['filter' => 'permit:viewCashflow']);
	$routes->post('cashflow/save', 'Cashflow::create', ['filter' => 'permit:createCashflow']);
	$routes->put('cashflow/update/(:segment)', 'Cashflow::update/$1', ['filter' => 'permit:updateCashflow']);
	$routes->delete('cashflow/delete/(:segment)', 'Cashflow::delete/$1', ['filter' => 'permit:deleteCashflow']);
});