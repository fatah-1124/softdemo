<?php

namespace App\Controllers;
use Exception;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Home extends BaseController
{
	function __construct()
	{
		$config = config("App");
		if (!session()->get('lang')) :
		session()->set('lang', env('app.defaultLocale') ?? $config->defaultLocale);
		endif;
	}
	
	public function index()
	{
		return view('home', []);
	}

	public function setLanguage()
	{
		$lang = $this->request->getUri()->getSegments()[1];
		$this->session->set("lang", $lang);
		return redirect()->back()->with('success', 'Language successfully changed to ' . $lang);
	}

	public function test()
	{
		/**
		 * Install the printer using USB printing support, and the "Generic / Text Only" driver,
		 * then share it (you can use a firewall so that it can only be seen locally).
		 *
		 * Use a WindowsPrintConnector with the share name to print.
		 *
		 * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
		 * "Receipt Printer), the following commands work:
		 *
		 *  echo "Hello World" > testfile
		 *  copy testfile "\\%COMPUTERNAME%\Receipt Printer"
		 *  del testfile
		 */
		try {
			// Enter the share name for your USB printer here
			//$connector = null;
			$printer = env('printerShareName');
			$connector = new WindowsPrintConnector($printer);

			/* Print a "Hello world" receipt" */
			$printer = new Printer($connector);
			$printer->text("\n");
			$printer->text("\n");
			$printer->text("Hello World!\n");
			$printer->text("\n");
			$printer->text("\n");
			$printer->cut();

			/* Close printer */
			$printer->close();
		} catch (Exception $e) {
			echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
		}
	}

	//--------------------------------------------------------------------

}
