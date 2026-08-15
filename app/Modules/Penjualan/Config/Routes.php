<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('sales', ['filter' => 'auth', 'namespace' => 'App\Modules\Penjualan\Controllers'], function($routes){
	$routes->get('/', 'Pointofsales::index', ['filter' => 'permit:createPenjualan']);
});

$routes->group('penjualan', ['filter' => 'auth', 'namespace' => 'App\Modules\Penjualan\Controllers'], function($routes){
	$routes->get('/', 'Penjualan::index', ['filter' => 'permit:viewPenjualan']);
	$routes->get('printnota-html', 'Penjualan::printNotaHtml');
	$routes->get('print-invoice-a4', 'Penjualan::printInvoiceA4');
	//$routes->get('printnota-pdf', 'Penjualan::printNotaPdf');
	$routes->get('(:segment)/edit', 'Penjualan::edit/$1', ['filter' => 'permit:updatePenjualan']);
});

$routes->group('api', ['filter' => 'jwtauth', 'namespace' => 'App\Modules\Penjualan\Controllers\Api'], function($routes){
	$routes->get('penjualan', 'Penjualan::index', ['filter' => 'permit:viewPenjualan']);
	$routes->post('penjualan/save/cash', 'Penjualan::create', ['filter' => 'permit:createPenjualan']);
	$routes->post('penjualan/save/credit', 'Penjualan::create1', ['filter' => 'permit:createPenjualan']);
	$routes->get('penjualan/(:segment)', 'Penjualan::show/$1', ['filter' => 'permit:viewPenjualan']);
	$routes->put('penjualan/update/(:segment)/cash', 'Penjualan::update/$1', ['filter' => 'permit:updatePenjualan']);
	$routes->put('penjualan/update/(:segment)/credit', 'Penjualan::update1/$1', ['filter' => 'permit:updatePenjualan']);
	$routes->delete('penjualan/delete/(:segment)', 'Penjualan::delete/$1', ['filter' => 'permit:deletePenjualan']);
	$routes->get('cetaknota/(:segment)', 'Penjualan::cetakNota/$1');
	$routes->post('penjualan/cetakusb', 'Penjualan::cetakUSB');
	$routes->post('penjualan/cetakbluetooth', 'Penjualan::cetakBluetooth');

	$routes->post('penjualan/item/save', 'PenjualanItem::create', ['filter' => 'permit:createPenjualan']);
	$routes->get('penjualan/item/get/(:segment)', 'PenjualanItem::getItem/$1');
	$routes->put('penjualan/item/update/(:segment)', 'PenjualanItem::updateItem/$1', ['filter' => 'permit:updatePenjualan']);
	$routes->delete('penjualan/item/delete/(:segment)', 'PenjualanItem::delete/$1', ['filter' => 'permit:deletePenjualan']);
});