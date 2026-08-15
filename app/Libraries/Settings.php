<?php

namespace App\Libraries;
class Settings
{
	var $info = array();
	public function __construct()
	{
		$DB = \Config\Database::connect();
		$site = $DB->table('settings');

		foreach ($site->get()->getResult() as $set) {
			$key = $set->variable_setting;
			$value = $set->value_setting;
			$this->info[$key] = $value;
		}
	}
}
