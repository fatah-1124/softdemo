<?php

namespace App\Modules\Biaya\Controllers\Api;
/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (https://Tokopedia.com/itshoppwt, https://Shopee.co.id/itshoppwt, https://Toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
02-2023
*/

use App\Controllers\BaseControllerApi;
use App\Modules\Biaya\Models\BiayaModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\I18n\Time;

class Biaya extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = BiayaModel::class;
    protected $toko;
    protected $cashflow;
    protected $log;

    public function __construct()
    {
        //memanggil Model
        $this->toko = new TokoModel();
        $this->cashflow = new CashflowModel();
        $this->log = new LogModel();
        helper('text');
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        if ($start == "" && $end == "") {
            $data = $this->model->getBiaya();
        } else {
            $data = $this->model->getBiaya($start, $end);
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
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->showPajak($id)], 200);
    }

    public function create()
    {
        $rules = [
            'tanggal' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'jenis' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nominal' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        //Ambil kode biaya
        $toko = $this->toko->first();
        $kdBiaya = $toko['kode_biaya'];
        $kdJualTahun = $toko['kode_jual_tahun'];
        /* $time = Time::now();
        if ($time->getHour() < 10) {
            $getHour = '0' . $time->getHour();
        } else {
            $getHour = $time->getHour();
        }
        if ($time->getMinute() < 10) {
            $getMinute = '0' . $time->getMinute();
        } else {
            $getMinute = $time->getMinute();
        }
        if ($time->getSecond() < 10) {
            $getSecond = '0' . $time->getSecond();
        } else {
            $getSecond = $time->getSecond();
        }
        $timestamp = $getHour . $getMinute . $getSecond; */

        if ($kdJualTahun == '1') {
            // Hitung transaksi per hari (opsional per toko)
            $query = $this->model
                ->select('DATE(created_at) as date_val, COUNT(*) as total')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->groupBy('DATE(created_at)');
            $hasil = $query->get()->getRowArray();
            $last  = empty($hasil) ? 1 : $hasil['total'] + 1;
            $lastKode  = sprintf('%02s', $last);
            $kodeBiaya = $kdBiaya . date('dmy') . $lastKode;
        } else {
            // Hitung transaksi per bulan (opsional per toko)
            $query = $this->model
                ->select('YEAR(created_at) as year_val, MONTH(created_at) as month_val, COUNT(*) as total')
                ->where('YEAR(created_at)', date('Y'))
                ->where('MONTH(created_at)', date('m'))
                ->groupBy('YEAR(created_at), MONTH(created_at)');
            $hasil = $query->get()->getRowArray();
            $last  = empty($hasil) ? 1 : $hasil['total'] + 1;
            $lastKode  = sprintf('%02s', $last);
            $kodeBiaya = $kdBiaya . date('my') . $lastKode;
        }

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $jenis = $json->jenis;
            $tanggal = $json->tanggal;
            $waktu = date('H:i:s');
            $nominal = $json->nominal;
            $data = [
                'faktur' => $kodeBiaya,
                'tanggal' => $tanggal,
                'waktu' => $waktu,
                'jenis' => $jenis,
                'nominal' => $nominal,
                'keterangan' => $json->keterangan,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        } else {
            $jenis = $this->request->getPost('jenis');
            $tanggal = $this->request->getPost('tanggal');
            $waktu = date('H:i:s');
            $nominal = $this->request->getPost('nominal');
            $data = [
                'faktur' => $kodeBiaya,
                'tanggal' => $tanggal,
                'waktu' => $waktu,
                'jenis' => $jenis,
                'nominal' => $nominal,
                'keterangan' => $this->request->getPost('keterangan'),
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
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
            //Save Biaya
            $this->model->save($data);
            $idBiaya = $this->model->getInsertID();
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Biaya: ' . $kodeBiaya]);

            //Data Cashflow
            $dataKas = [
                'faktur' => $kodeBiaya,
                'jenis' => 'Pengeluaran',
                'kategori' => $jenis,
                'tanggal' => date('Y-m-d', strtotime($tanggal)),
                'waktu' => date('H:i:s', strtotime($waktu)),
                'pemasukan' => 0,
                'pengeluaran' => $nominal,
                'keterangan' => 'Biaya: ' . $kodeBiaya,
                'id_biaya' => $idBiaya,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            //Save Kas
            $this->cashflow->save($dataKas);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $kodeBiaya]);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
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
            //Save Update
            $this->model->update($id, $data);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Biaya: ' . $id]);

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
        $faktur = $delete['faktur'];
        if ($delete) {
            //Cari data Cashflow
            $cash = $this->cashflow->where('faktur', $faktur)->findAll();
            if ($cash) :
                foreach ($cash as $row) {
                    $idCash = $row['id_cashflow'];
                    //Hapus Cashflow
                    $this->cashflow->delete($idCash);
                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Cashflow: ' . $idCash]);
                }
            endif;

            //Hapus Biaya
            $this->model->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Biaya: ' . $faktur]);

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
}
