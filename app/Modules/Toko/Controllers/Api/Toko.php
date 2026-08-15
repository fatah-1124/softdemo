<?php

namespace App\Modules\Toko\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Biaya\Models\BiayaModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Hutang\Models\HutangBayarModel;
use App\Modules\Hutang\Models\HutangModel;
use App\Modules\Keranjang\Models\KeranjangModel;
use App\Modules\Keranjang\Models\OrderModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Log\Models\LogModel;
use App\Modules\Media\Models\MediaModel;
use App\Modules\Pajak\Models\PajakModel;
use App\Modules\Pembelian\Models\PembelianItemModel;
use App\Modules\Pembelian\Models\PembelianModel;
use App\Modules\Penjualan\Models\PenjualanItemModel;
use App\Modules\Penjualan\Models\PenjualanModel;
use App\Modules\Piutang\Models\PiutangBayarModel;
use App\Modules\Piutang\Models\PiutangModel;

class Toko extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = TokoModel::class;
    protected $log;
    protected $penjualan;
    protected $penjualanItem;
    protected $pembelian;
    protected $pembelianItem;
    protected $piutang;
    protected $piutangBayar;
    protected $hutang;
    protected $hutangBayar;
    protected $cashflow;
    protected $pajak;
    protected $biaya;
    protected $barang;
    protected $media;
    protected $keranjang;
    protected $orders;

    public function __construct()
    {
        $this->log = new LogModel();
        $this->penjualan = new PenjualanModel();
        $this->penjualanItem = new PenjualanItemModel();
        $this->pembelian = new PembelianModel();
        $this->pembelianItem = new PembelianItemModel();
        $this->piutang = new PiutangModel();
        $this->piutangBayar = new PiutangBayarModel();
        $this->hutang = new HutangModel();
        $this->hutangBayar = new HutangBayarModel();
        $this->cashflow = new CashflowModel();
        $this->pajak = new PajakModel();
        $this->biaya = new BiayaModel();
        $this->barang = new BarangModel();
        $this->media = new MediaModel();
        $this->keranjang = new KeranjangModel();
        $this->orders = new OrderModel();
    }

    public function index()
    {
        $data = $this->model->first();
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        //$id = '1';
        $rules = [
            'nama_toko' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'alamat_toko' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'telp' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama_pemilik' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nib' => [
                'rules'  => 'required|numeric',
                'errors' => []
            ],
            'ppn' => [
                'rules'  => 'required|numeric',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'nama_toko' => $json->nama_toko,
                'alamat_toko' => $json->alamat_toko,
                'telp' => $json->telp,
                'email' => $json->email,
                'nama_pemilik' => $json->nama_pemilik,
                'NIB' => $json->nib,
                'PPN' => $json->ppn,
                'include_ppn' => $json->include_ppn,
                'kode_barang' => $json->kode_barang,
                'kode_jual' => $json->kode_jual,
                'kode_beli' => $json->kode_beli,
                'kode_kas' => $json->kode_kas,
                'kode_pajak' => $json->kode_pajak,
                'kode_biaya' => $json->kode_biaya,
                'paper_size' => $json->paper_size,
                'footer_nota' => $json->footer_nota,
                'jatuhtempo_hari' => $json->jatuhtempo_hari,
                'jatuhtempo_tanggal' => $json->jatuhtempo_tanggal,
                'pembulatan' => $json->pembulatan,
                'pembulatan_keatas' => $json->pembulatan_keatas,
                'pembulatan_max' => $json->pembulatan_max,
                'diskon_member' => $json->diskon_member,
                'footer_invoice_ttd1' => $json->footer_invoice_ttd1,
                'footer_invoice_ttd2' => $json->footer_invoice_ttd2,
                'footer_keterangan' => $json->footer_keterangan,
                'margin_barang' => $json->margin_barang
            ];
        } else {
            $data = $this->request->getRawInput();
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Toko/Warung: ' . $id]);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifPrinterUsb($id = NULL)
    {

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'printer_usb' => $json->printer_usb
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'printer_usb' => $input['printer_usb']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifPrinterBT($id = NULL)
    {

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'printer_bluetooth' => $json->printer_bluetooth
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'printer_bluetooth' => $input['printer_bluetooth']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifKodeJualTahun($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'kode_jual_tahun' => $json->kode_jual_tahun
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'kode_jual_tahun' => $input['kode_jual_tahun']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifScanKeranjang($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'scan_keranjang' => $json->scan_keranjang
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'scan_keranjang' => $input['scan_keranjang']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifTglJatuhTempo($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'jatuhtempo_hari_tanggal' => $json->jatuhtempo_hari_tanggal
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'jatuhtempo_hari_tanggal' => $input['jatuhtempo_hari_tanggal']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setAktifKetJatuhTempo($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'jatuhtempo_keterangan' => $json->jatuhtempo_keterangan
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'jatuhtempo_keterangan' => $input['jatuhtempo_keterangan']
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function resetDataTransaksi()
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $confirm = $json->confirm;
        } else {
            $input = $this->request->getRawInput();
            $confirm = $input['confirm'];
        }

        if ($confirm) {
            $db      = \Config\Database::connect();
            $db->simpleQuery("SET FOREIGN_KEY_CHECKS = 0");
            $this->piutang->truncate();
            $this->piutangBayar->truncate();
            $this->penjualan->truncate();
            $this->penjualanItem->truncate();
            $this->hutang->truncate();
            $this->hutangBayar->truncate();
            $this->pembelian->truncate();
            $this->pembelianItem->truncate();
            $this->cashflow->truncate();
            $this->pajak->truncate();
            $this->biaya->truncate();
            $this->barang->truncate();
            $this->media->truncate();
            $this->keranjang->truncate();
            $this->orders->truncate();
            $this->log->truncate();
            $db->simpleQuery("SET FOREIGN_KEY_CHECKS = 1");

            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }
}
