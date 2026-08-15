<?php

namespace  App\Modules\Toko\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;

class Toko extends BaseController
{
	protected $setting;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
	}

	public function index()
	{
		return view('App\Modules\Toko\Views/toko', [
			'title' => lang('App.settings') . ' Toko/Warung'
		]);
	}

}
