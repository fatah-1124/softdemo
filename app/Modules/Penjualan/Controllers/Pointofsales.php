<?php

namespace  App\Modules\Penjualan\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Bank\Models\BankAkunModel;
use CodeIgniter\I18n\Time;

class Pointofsales extends BaseController
{
	protected $setting;
	protected $toko;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->toko = new TokoModel();
	}

	public function index()
	{
		$toko = $this->toko->first();
		$jatuhtempo = $toko['jatuhtempo_hari_tanggal'];
		$tempoHari = $toko['jatuhtempo_hari'];
		$tempoTanggal = $toko['jatuhtempo_tanggal'];
		if ($jatuhtempo == 0) {
			$tanggalTempo = Date('Y-m-d', strtotime("+$tempoHari days"));
			$hariTempo = $tempoHari;
		} else {
			$date1 = new \DateTime(date("Y-m-d"));
			$date2 = new \DateTime(date('Y-m-' . $tempoTanggal, strtotime("+1 months")));
			$diff = $date2->diff($date1);
			$tanggalTempo = $date2->format('Y-m-d');
			$hariTempo = $diff->d;
		}
		return view('App\Modules\Penjualan\Views/point_of_sales', [
			'title' => 'Point of Sales (POS)',
			'namaToko' => $toko['nama_toko'],
			'cetakUSB' => $toko['printer_usb'],
			'cetakBluetooth' => $toko['printer_bluetooth'],
			'scanKeranjang' => $toko['scan_keranjang'],
			'ppn' => $toko['PPN'],
			'logo' => $this->setting->info['img_logo'],
			'cashierpayPos' => $this->setting->info['cashierpay_position'],
			'jatuhTempo' => $tanggalTempo,
			'tempoHari' => $hariTempo,
			'pembulatan' => $toko['pembulatan'],
			'pembulatan_keatas' => $toko['pembulatan_keatas'],
			'pembulatan_max' => $toko['pembulatan_max'],
			'navbarColor' => $this->setting->info['navbar_color'],
			'ppnInclude' => $toko['include_ppn']
		]);
	}
}
