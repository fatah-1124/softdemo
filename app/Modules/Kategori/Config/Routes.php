<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('kategori', ['filter' => 'auth', 'namespace' => 'App\Modules\Kategori\Controllers'], function($routes){
	$routes->get('/', 'Kategori::index');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Kategori\Controllers\Api'], function($routes){
	$routes->get('kategori', 'Kategori::index');
	$routes->get('kategori/(:segment)', 'Kategori::show/$1');
	$routes->post('kategori/save', 'Kategori::create');
	$routes->put('kategori/update/(:segment)', 'Kategori::update/$1');
	$routes->delete('kategori/delete/(:segment)', 'Kategori::delete/$1');
});