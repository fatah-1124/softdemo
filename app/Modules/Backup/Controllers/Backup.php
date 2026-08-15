<?php

namespace  App\Modules\Backup\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;

class Backup extends BaseController
{
	protected $setting;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
	}

	public function index()
	{
		return view('App\Modules\Backup\Views/backup', [
			'title' => 'Backup DB'
		]);
	}

}
