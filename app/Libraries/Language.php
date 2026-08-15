<?php

namespace App\Libraries;
class Language
{
	var $htmlLang = "";
	var $siteLang = "";
	public function __construct()
	{
		$config = config("App");
		if (session()->get('lang') || env('app.defaultLocale') == 'id' ?? $config->defaultLocale) {
			$this->htmlLang = 'id';
			$this->siteLang = 'id-ID';
		} else {
			$this->htmlLang = 'en';
			$this->siteLang = 'en-US';
		}
	}
}
