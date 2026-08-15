<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('/', ['namespace' => 'App\Modules\Auth\Controllers'], function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->get('register', 'Auth::register');
    $routes->get('logout', 'Auth::logout');
    $routes->get('verify_email', "Auth::verifyEmail");
});

$routes->group('password', ['namespace' => 'App\Modules\Auth\Controllers'], function ($routes) {
    $routes->get('reset', 'Auth::passwordReset');
    $routes->get('change', 'Auth::passwordChange');
});

$routes->group('auth', ['namespace' => 'App\Modules\Auth\Controllers\Api'], function ($routes) {
    $routes->post('login', 'Auth::login');
	$routes->post('register', 'Auth::register');
	$routes->post('resetPassword', 'Auth::resetPassword');
	$routes->post('changePassword', 'Auth::changePassword');
});