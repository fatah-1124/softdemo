<?php

namespace  App\Modules\Setting\Controllers;
/*
DeepWater Solution
Madiun, Jawa Timur, Indonesia
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;

class Setting extends BaseController
{
	protected $setting;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
	}

	public function index()
	{
		return view('App\Modules\Setting\Views/setting', [
			'title' => lang('App.settings')
		]);
	}

}
