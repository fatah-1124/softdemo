<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('backup', ['filter' => 'auth', 'namespace' => 'App\Modules\Backup\Controllers'], function($routes){
	$routes->get('/', 'Backup::index', ['filter' => 'permit:viewBackup']);
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Backup\Controllers\Api'], function($routes){
    $routes->get('backup', 'Backup::index', ['filter' => 'permit:viewBackup']);
	$routes->post('backup/save', 'Backup::create', ['filter' => 'permit:createBackup']);
	$routes->delete('backup/delete/(:segment)', 'Backup::delete/$1', ['filter' => 'permit:deleteBackup']);
	$routes->post('backup/download', 'Backup::download', ['filter' => 'permit:createBackup']);
});