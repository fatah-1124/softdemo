<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('media', ['filter' => 'auth', 'namespace' => 'App\Modules\Media\Controllers'], function($routes){
	$routes->get('/', 'Media::index');
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Media\Controllers\Api'], function($routes){
	$routes->get('media/(:segment)', 'Media::getMedia/$1');
	$routes->post('media/save', 'Media::create');
	$routes->delete('media/delete/(:segment)', 'Media::delete/$1');
});