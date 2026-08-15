<?php

namespace App\Modules\Piutang\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Piutang\Models\PiutangModel;
use App\Modules\Bank\Models\BankModel;
use App\Modules\Piutang\Models\PiutangBayarModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Kontak\Models\KontakModel;
use App\Modules\Penjualan\Models\PenjualanModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\I18n\Time;

class Piutang extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PiutangModel::class;
    protected $piutangBayar;
    protected $penjualan;
    protected $cashflow;
    protected $bank;
    protected $toko;
    protected $log;
    protected $kontak;

    public function __construct()
    {
        //memanggil Model
        $this->piutangBayar = new PiutangBayarModel();
        $this->penjualan = new PenjualanModel();
        $this->cashflow = new CashflowModel();
        $this->bank = new BankModel();
        $this->toko = new TokoModel();
        $this->log = new LogModel();
        $this->kontak = new KontakModel();
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        $where = $input['status'] ?? "";
        if ($start == "" && $end == "" && $where == "") {
            $data = $this->model->getPiutang();
        } else {
            $data = $this->model->getPiutang($start, $end, $where);
        }

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

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->piutangBayar->where("id_piutang", $id)->findAll()], 200);
    }

    public function total()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->select('sum(jumlah_piutang) as total')->where('status_piutang', 0)->get()->getRow()->total], 200);
    }

    public function create()
    {
        $rules = [
            'id_piutang' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nominal' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $idPiutang = $json->id_piutang;
            $faktur = $json->faktur;
            $nominal = $json->nominal;

            $data = [
                'id_piutang' => $idPiutang,
                'nominal' => $nominal,
                'id_login' => session()->get('id'),
            ];
        } else {
            $idPiutang = $this->request->getPost('id_piutang');
            $faktur = $this->request->getPost('faktur');
            $nominal = $this->request->getPost('nominal');

            $data = [
                'id_piutang' => $idPiutang,
                'nominal' => $nominal,
                'id_login' => session()->get('id'),
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->piutangBayar->save($data);
            $idPiutangBayar = $this->piutangBayar->getInsertID();
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Piutang Bayar ID: ' . $idPiutangBayar]);

            //Ambil Data Piutang
            $piutang = $this->model->find($idPiutang);
            $idPenjualan = $piutang['id_penjualan'];
            $jumlahPiutang = (int)$piutang['jumlah_piutang'];
            $jumlahBayar = (int)$piutang['jumlah_bayar'];
            $sisaPiutang = (int)$piutang['sisa_piutang'];
            $dataPiutang = [
                'jumlah_bayar' => $nominal + $jumlahBayar,
                'sisa_piutang' => $sisaPiutang - $nominal,
            ];
            $this->model->update($idPiutang, $dataPiutang);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Piutang ID: ' . $idPiutang]);

            //Data Cashflow
            $dataKas = [
                'faktur' => $faktur,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penjualan',
                'tanggal' => date('Y-m-d', strtotime(Time::now())),
                'waktu' => date('H:i:s', strtotime(Time::now())),
                'pemasukan' => $nominal,
                'pengeluaran' => 0,
                'keterangan' => 'Pembayaran Piutang Penjualan: ' . $faktur,
                'id_penjualan' => $idPenjualan,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            $this->cashflow->save($dataKas);
            $idCashflow = $this->cashflow->getInsertID();
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $faktur]);

            //Update Piutang Bayar untuk masukkan id_cashflow
            $this->piutangBayar->update($idPiutangBayar, ['id_cashflow' => $idCashflow]);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Piutang Bayar ID: ' . $idPiutangBayar]);

            //Ambil Sisa Piutang
            $sisa = $this->model->find($idPiutang);
            $idSisa = $sisa['id_penjualan'];
            $jumlahSisa = $sisa['jumlah_piutang'];
            $sisanya = $sisa['sisa_piutang'];
            $statusnya = $sisa['status_piutang'];
            //Update penjualan
            $penjualan = $this->penjualan->find($idSisa);
            $bayar = $penjualan['bayar'];
            if ($sisanya != 0) :
                $this->penjualan->update($idSisa, ['bayar' => $penjualan['bayar'] + $nominal, 'kembali' => $penjualan['kembali'] + $nominal]);
            endif;
            if ($sisanya == 0) :
                $this->model->update($idPiutang, ['status_piutang' => 1]);
                $this->penjualan->update($idSisa, ['bayar' => $bayar + $jumlahSisa, 'kembali' => 0]);
            endif;

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => ['sisa_piutang' => $sisanya],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'keterangan' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'keterangan' => $json->keterangan,
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
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Piutang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $delete = $this->model->find($id);
        $idPenjualan = $delete['id_penjualan'];
        $jumlahPiutang = $delete['jumlah_piutang'];
        $jumlahBayar = $delete['jumlah_bayar'];
        if ($delete) {
            //Cari Penjualan
            $penjualan = $this->penjualan->find($idPenjualan);
            $faktur = $penjualan['faktur'];
            //Data Cashflow
            $dataKas = [
                'faktur' => $faktur,
                'jenis' => 'Pemasukan',
                'kategori' => 'Penjualan',
                'tanggal' => date('Y-m-d', strtotime(Time::now())),
                'waktu' => date('H:i:s', strtotime(Time::now())),
                'pemasukan' => $jumlahPiutang,
                'pengeluaran' => 0,
                'keterangan' => 'Penjualan: ' . $faktur,
                'id_penjualan' => $idPenjualan,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            //Save Cashflow
            $this->cashflow->save($dataKas);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $faktur]);

            //Update penjualan
            $penjualan = $this->penjualan->find($idPenjualan);
            $this->penjualan->update($idPenjualan, ['bayar' => $penjualan['total'], 'kembali' => 0]);

            //Hapus Piutang berdasarkan $id
            $this->model->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Piutang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete2($id = null)
    {
        $delete = $this->piutangBayar->find($id);
        $idPiutang = $delete['id_piutang'];
        $nominal = $delete['nominal'];
        $idCashflow = $delete['id_cashflow'];
        if ($delete) {
            //Cari Data Piutang berdasarkan $idPiutang
            $piutang = $this->model->find($idPiutang);
            $idPenjualan = $piutang['id_penjualan'];
            $jumlahBayar = $piutang['jumlah_bayar'];
            $sisaPiutang = $piutang['sisa_piutang'];
            $dataPiutang = [
                'jumlah_bayar' => $jumlahBayar - $nominal,
                'sisa_piutang' => $sisaPiutang + $nominal,
            ];
            $this->model->update($idPiutang, $dataPiutang);

            //Cari data Cashflow
            $cash = $this->cashflow->find($idCashflow);
            if ($cash) :
                //Hapus Cashflow
                $this->cashflow->delete($idCashflow);
                //Save Log
                $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Cashflow: ' . $idCashflow]);
            endif;

            //Ambil Sisa Piutang
            $sisa = $this->model->find($idPiutang);
            $sisanya = $sisa['sisa_piutang'];
            $jumlahSisa = $sisa['jumlah_piutang'];
            $statusnya = $sisa['status_piutang'];

            //Update penjualan
            $penjualan = $this->penjualan->find($idPenjualan);
            $this->penjualan->update($idPenjualan, ['bayar' => $penjualan['bayar'] - $nominal, 'kembali' => $penjualan['kembali'] - $nominal]);
            if ($sisanya == $nominal) :
                $this->model->update($idPiutang, ['status_piutang' => 0]);
            endif;

            //Hapus Piutang Bayar berdasarkan $id
            $this->piutangBayar->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Piutang Bayar: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => ['sisa_piutang' => $sisanya],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function findPiutang($id = null)
    {
        $kontak = $this->kontak->find($id);
        $namaKontak = $kontak['nama'];
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->getPiutangByKontak($id), 'nama_kontak' => $namaKontak, 'total_piutang' => $this->model->totalPiutangByKontak($id)], 200);
    }
}
