<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('toko', ['filter' => 'auth', 'namespace' => 'App\Modules\Toko\Controllers'], function($routes){
	$routes->get('/', 'Toko::index', ['filter' => 'permit:viewConfig']);
});


$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Toko\Controllers\Api'], function($routes){
	$routes->get('toko', 'Toko::index', ['filter' => 'permit:viewConfig']);
	$routes->put('toko/update/(:segment)', 'Toko::update/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktifprinterusb/(:segment)', 'Toko::setAktifPrinterUsb/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktifprinterbt/(:segment)', 'Toko::setAktifPrinterBT/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktifkodejualtahun/(:segment)', 'Toko::setAktifKodeJualTahun/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktifscankeranjang/(:segment)', 'Toko::setAktifScanKeranjang/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktiftgljatuhtempo/(:segment)', 'Toko::setAktifTglJatuhTempo/$1', ['filter' => 'permit:updateConfig']);
	$routes->put('toko/setaktifketjatuhtempo/(:segment)', 'Toko::setAktifKetJatuhTempo/$1', ['filter' => 'permit:updateConfig']);
	$routes->post('toko/resetdatatransaksi', 'Toko::resetDataTransaksi', ['filter' => 'permit:updateConfig']);
});