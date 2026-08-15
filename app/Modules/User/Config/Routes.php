<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('user', ['filter' => 'auth', 'namespace' => 'App\Modules\User\Controllers'], function($routes){
	$routes->get('/', 'User::index', ['filter' => 'permit:viewUser']);
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\User\Controllers\Api'], function($routes){
	$routes->get('users', 'User::index', ['filter' => 'permit:viewUser']);
	$routes->get('user/(:segment)', 'User::show/$1', ['filter' => 'permit:viewUser']);
	$routes->post('user/save', 'User::create', ['filter' => 'permit:createUser']);
	$routes->put('user/update/(:segment)', 'User::update/$1', ['filter' => 'permit:updateUser']);
	$routes->delete('user/delete/(:segment)', 'User::delete/$1', ['filter' => 'permit:deleteUser']);
	$routes->put('user/setactive/(:segment)', 'User::setActive/$1', ['filter' => 'permit:updateUser']);
	$routes->put('user/setrole/(:segment)', 'User::setRole/$1', ['filter' => 'permit:updateUser']);
	$routes->post('user/changepassword', 'User::changePassword', ['filter' => 'permit:updateUser']);
	$routes->put('user/setgroup/(:segment)', 'User::setGroup/$1', ['filter' => 'permit:updateUser']);
});