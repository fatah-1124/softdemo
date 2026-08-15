<?php

namespace App\Modules\Barang\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Penjualan\Models\PenjualanModel;
use App\Modules\Toko\Models\TokoModel;
use Ramsey\Uuid\Uuid;
use ShortUUID\ShortUUID;
use \Milon\Barcode\DNS1D;

class Barang extends BaseController
{
    protected $barang;
    protected $penjualan;
    protected $setting;
    protected $toko;

    public function __construct()
    {
        //memanggil Model
        $this->barang = new BarangModel();
        $this->penjualan = new PenjualanModel();
        $this->setting = new Settings();
        $this->toko = new TokoModel();
    }

    public function index()
    {
        $cari = $this->request->getVar('search');
        return view('App\Modules\Barang\Views/barang', [
            'title' => lang('App.items'),
            'search' => $cari
        ]);
    }

    public function add()
    {
        $uuid = Uuid::uuid4();
        //$suuid = new ShortUUID();
        $toko = $this->toko->first();
        return view('App\Modules\Barang\Views/barang_baru', [
            'title' => lang('App.add'),
            'uuid' => $uuid->toString(),
            //'uuid' => $suuid->encode($uuid),
            'marginBarang' => $toko['margin_barang']
        ]);
    }

    public function edit($id = null)
    {
        $data = $this->barang->where('uuid_barang', $id)->first();

        return view('App\Modules\Barang\Views/barang_edit', [
            'title' => lang('App.edit'),
            'data' => $data,
        ]);
    }

    public function barcode()
    {
        $id = $this->request->getVar('id_barang');
        $str = $this->request->getVar('text');
        $jml = $this->request->getVar('jumlah');

        if ($str == "") :
            $this->session->setFlashdata('error', 'Barang id= ' .$id . ' tidak memiliki Barcode');
            return redirect()->to('/barang');
        endif;
        
        $this->generateBarcode($id, $str, $jml);
    }

    private function generateBarcode($id, $string, $jumlah)
    {
        helper('text');
        $barang = $this->barang->find($id);
        $toko = $this->toko->first();

        $barcode = new DNS1D();
        $barcode->setStorPath(WRITEPATH . 'cache/');

        echo view("App\Modules\Barang\Views/barcode", [
            "barcode" => $barcode,
            "text" => $string,
            "tipe" => 'C128',
            "jumlah" => $jumlah,
            "barang" => $barang,
            "toko" => $toko,
        ]);
    }

    public function labelRack()
    {
        $str = $this->request->getVar('text');
        $jml = $this->request->getVar('jumlah');
        $this->generateLabel($str, $jml);
    }

    private function generateLabel($string, $jumlah)
    {
        helper('text');
        $barang = $this->barang->showBarang($string);
        $namaBarang = $barang['nama_barang'];
        $hargaJual = $barang['harga_jual'];
        $toko = $this->toko->first();

        $barcode = new DNS1D();
        $barcode->setStorPath(WRITEPATH . 'cache/');

        echo view("App\Modules\Barang\Views/label", [
            "barcode" => $barcode,
            "namaToko" => $toko['nama_toko'],
            "namaBarang" => character_limiter($namaBarang, 40, '...'),
            "hargaJual" => $hargaJual,
            "text" => $string,
            "tipe" => 'C128',
            "jumlah" => $jumlah,
            "barang" => $barang
        ]);
    }
}
