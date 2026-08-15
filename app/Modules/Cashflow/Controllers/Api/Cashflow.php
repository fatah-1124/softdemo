<?php

namespace App\Modules\Cashflow\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Bank\Models\BankModel;
use App\Modules\Biaya\Models\BiayaModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Log\Models\LogModel;
use App\Modules\Pembelian\Models\PembelianModel;
use App\Modules\Penjualan\Models\PenjualanModel;
use CodeIgniter\I18n\Time;

class Cashflow extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = CashflowModel::class;
    protected $penjualan;
    protected $pembelian;
    protected $bank;
    protected $toko;
    protected $log;
    protected $biaya;

    public function __construct()
    {
        //memanggil Model
        $this->penjualan = new PenjualanModel();
        $this->pembelian = new PembelianModel();
        $this->bank = new BankModel();
        $this->toko = new TokoModel();
        $this->log = new LogModel();
        $this->biaya = new BiayaModel();
        helper('text');
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        if ($start == "" && $end == "") {
            $data = $this->model->getCashflow();
        } else {
            $data = $this->model->getCashflow($start, $end);
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
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->showKas($id)], 200);
    }

    public function saldo()
    {
        $data = $this->model->getSaldo();
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => (int)$data
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

    public function create()
    {
        $rules = [
            'jenis' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'kategori' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nominal' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        //Ambil kode Kas
        $toko = $this->toko->first();
        $kdKas = $toko['kode_kas'];
        $idBankAkun = $toko['id_bank_akun'];
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
            // Hitung transaksi tgl-bulan-tahun total tambah 1 (harian)
            $query = $this->model
                ->select('DATE(created_at) as date_val, COUNT(*) as total')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->groupBy('DATE(created_at)');
            $hasil = $query->get()->getRowArray();
            $last = empty($hasil) ? 1 : $hasil['total'] + 1;
            $lastKode = sprintf('%02s', $last);
            $kodeKas = $kdKas . date('dmy') . $lastKode;
        } else {
            // Hitung transaksi bulan-tahun total tambah 1 (bulanan)
            $query = $this->model
                ->select('YEAR(created_at) as year_val, MONTH(created_at) as month_val, COUNT(*) as total')
                ->where('YEAR(created_at)', date('Y'))
                ->where('MONTH(created_at)', date('m'))
                ->groupBy('YEAR(created_at), MONTH(created_at)');
            $hasil = $query->get()->getRowArray();
            $last = empty($hasil) ? 1 : $hasil['total'] + 1;
            $lastKode = sprintf('%02s', $last);
            $kodeKas = $kdKas . date('my') . $lastKode;
        }

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $jenis = $json->jenis;
            $kategori = $json->kategori;
            $tanggal = $json->tanggal;
            $waktu = $json->waktu;
            $nominal = $json->nominal;
            if ($jenis == "Pemasukan") {
                $pemasukan = $nominal;
                $pengeluaran = 0;
            } else {
                $pengeluaran = $nominal;
                $pemasukan = 0;
            }
            $data = [
                'faktur' => $kodeKas,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'tanggal' => date('Y-m-d', strtotime($tanggal)),
                'waktu' => date('H:i:s', strtotime($waktu)),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'keterangan' => $json->keterangan,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        } else {
            $jenis = $this->request->getPost('jenis');
            $kategori = $this->request->getPost('kategori');
            $tanggal = $this->request->getPost('tanggal');
            $waktu = $this->request->getPost('waktu');
            $nominal = $this->request->getPost('nominal');
            if ($jenis == "Pemasukan") {
                $pemasukan = $nominal;
                $pengeluaran = 0;
            } else {
                $pengeluaran = $nominal;
                $pemasukan = 0;
            }
            $data = [
                'faktur' => $kodeKas,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'tanggal' => date('Y-m-d', strtotime($tanggal)),
                'waktu' => date('H:i:s', strtotime($waktu)),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
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
            $this->model->save($data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $kodeKas . ' Jenis: ' . $jenis]);

            if ($jenis == "Mutasi ke Bank") {
                $dataBank = [
                    'faktur' => $kodeKas,
                    'jenis' => 'Pemasukan',
                    'tanggal' => date('Y-m-d', strtotime($tanggal)),
                    'waktu' => date('H:i:s', strtotime($waktu)),
                    'pemasukan' => $nominal,
                    'pengeluaran' => 0,
                    'keterangan' => 'Mutasi dari Kas',
                    'id_toko' => 1,
                    'id_login' => session()->get('id'),
                    'id_bank_akun' => $idBankAkun,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null
                ];
                $this->bank->save($dataBank);
                //Save Log
                $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Bank: ' . $kodeKas . ' Keterangan: Pemasukan Mutasi dari Kas']);
            }

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
            'jenis' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'kategori' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nominal' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $jenis = $json->jenis;
            $kategori = $json->kategori;
            $tanggal = $json->tanggal;
            $waktu = $json->waktu;
            $nominal = $json->nominal;
            if ($jenis == "Pemasukan") {
                $pemasukan = $nominal;
                $pengeluaran = 0;
            } else {
                $pengeluaran = $nominal;
                $pemasukan = 0;
            }
            $data = [
                'jenis' => $jenis,
                'kategori' => $kategori,
                'tanggal' => date('Y-m-d', strtotime($json->tanggal)),
                'waktu' => date('H:i:s', strtotime($json->waktu)),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'keterangan' => $json->keterangan,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } else {
            $input = $this->request->getRawInput();
            $jenis = $input['jenis'];
            $kategori = $input['kategori'];
            $tanggal = $input['tanggal'];
            $waktu = $input['waktu'];
            $nominal = $input['nominal'];
            if ($jenis == "Pemasukan") {
                $pemasukan = $nominal;
                $pengeluaran = 0;
            } else {
                $pengeluaran = $nominal;
                $pemasukan = 0;
            }
            $data = [
                'jenis' => $jenis,
                'kategori' => $kategori,
                'tanggal' => date('Y-m-d', strtotime($tanggal)),
                'waktu' => date('H:i:s', strtotime($waktu)),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'keterangan' => $input['keterangan'],
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s')
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
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Cashflow: ' . $id]);

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
        $jenis = $delete['jenis'];

        //Cek data Penjualan dulu karena akan terkena Foreign key cek 'restrict'
        $cekPenjualan = $this->penjualan->where(['faktur' => $faktur])->findAll();
        if ($cekPenjualan) :
            $response = [
                'status' => true,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        //Cek data Pembelian dulu karena akan terkena Foreign key cek 'restrict'
        $cekPembelian = $this->pembelian->where(['faktur' => $faktur])->findAll();
        if ($cekPembelian) :
            $response = [
                'status' => true,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        //Cek data Biaya dulu karena akan terkena Foreign key cek 'restrict'
        $cekBiaya = $this->biaya->where(['faktur' => $faktur])->findAll();
        if ($cekBiaya) :
            $response = [
                'status' => true,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        if (!$cekPenjualan || !$cekPembelian || !$cekBiaya) {
            //Delete Bank
            if ($jenis == 'Mutasi ke Bank') :
                $bank = $this->bank->where('faktur', $faktur)->first();
                $this->bank->delete($bank['id_bank']);
            endif;
            //Delete ID Kas
            $this->model->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Cashflow: ' . $faktur]);
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Bank: ' . $faktur]);

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
