<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('group', ['filter' => 'auth', 'namespace' => 'App\Modules\Group\Controllers'], function ($routes) {
    $routes->get('/', 'Group::index', ['filter' => 'permit:viewGroup']);
    $routes->get('edit/(:segment)', 'Group::edit/$1', ['filter' => 'permit:viewGroup']);
    $routes->post('update/(:segment)', 'Group::update/$1', ['filter' => 'permit:updateGroup']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Group\Controllers\Api'], function($routes){
    $routes->get('groups', 'Group::index');
    $routes->get('group/(:segment)', 'Group::show/$1');
    $routes->post('group/save', 'Group::create');
    $routes->delete('group/delete/(:segment)', 'Group::delete/$1', ['filter' => 'permit:deleteGroup']);

});
