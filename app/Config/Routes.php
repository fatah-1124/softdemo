<?php

use CodeIgniter\Router\RouteCollection;

use App\Libraries\Settings;

/**
 * @var RouteCollection $routes
 */

$setting = new Settings();
if ($setting->info['enable_frontend'] == 'true') {
	$routes->get('/', 'Home::index');
} else {
	$routes->get('/', '\App\Modules\Auth\Controllers\Auth::login');
}

$routes->get('/restricted', 'Restricted::index', ['filter' => 'auth']);

/**
 * --------------------------------------------------------------------
 * HMVC Routing
 * --------------------------------------------------------------------
 */

foreach (glob(APPPATH . 'Modules/*', GLOB_ONLYDIR) as $item_dir) {
	if (file_exists($item_dir . '/Config/Routes.php')) {
		require_once($item_dir . '/Config/Routes.php');
	}
}

$routes->get('/test', 'Home::test');
$routes->get('/lang/{locale}', 'Home::setLanguage');

//Routes untuk Halaman admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
	$routes->get('/', 'Admin::index');
	$routes->get('export', 'Admin::export');
	$routes->get('export-tcpdf', 'Admin::exportTcpdf');
	$routes->get('export-mpdf', 'Admin::exportMpdf');
	$routes->get('export-excel', 'Admin::exportExcel');
});

//Contoh Routes untuk RESTful Api
$routes->group('api', ['filter' => 'jwtauth', 'namespace' => $routes->getDefaultNamespace() . 'Api'], function ($routes) {
});
