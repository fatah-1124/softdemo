<?php

namespace App\Modules\Hutang\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Hutang\Models\HutangModel;
use App\Modules\Bank\Models\BankModel;
use App\Modules\Hutang\Models\HutangBayarModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Pembelian\Models\PembelianModel;
use App\Modules\Pembelian\Models\PembelianItemModel;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\I18n\Time;

class Hutang extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = HutangModel::class;
    protected $hutangBayar;
    protected $pembelian;
    protected $cashflow;
    protected $bank;
    protected $toko;
    protected $itemBeli;
    protected $barang;
    protected $log;

    public function __construct()
    {
        //memanggil Model
        $this->hutangBayar = new HutangBayarModel();
        $this->pembelian = new PembelianModel();
        $this->cashflow = new CashflowModel();
        $this->bank = new BankModel();
        $this->toko = new TokoModel();
        $this->itemBeli = new PembelianItemModel();
        $this->barang = new BarangModel();
        $this->log = new LogModel();
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        $where = $input['status'] ?? "";
        if ($start == "" && $end == "" && $where == "") {
            $data = $this->model->getHutang();
        } else {
            $data = $this->model->getHutang($start, $end, $where);
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
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->hutangBayar->where("id_hutang", $id)->findAll()], 200);
    }

    public function total()
    {
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->model->select('sum(jumlah_hutang) as total')->where('status_hutang', 0)->get()->getRow()->total], 200);
    }

    public function create()
    {
        $rules = [
            'id_hutang' => [
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
            $idHutang = $json->id_hutang;
            $faktur = $json->faktur;
            $nominal = $json->nominal;

            $data = [
                'id_hutang' => $idHutang,
                'nominal' => $nominal,
                'id_login' => session()->get('id'),
            ];
        } else {
            $idHutang = $this->request->getPost('id_hutang');
            $faktur = $this->request->getPost('faktur');
            $nominal = $this->request->getPost('nominal');

            $data = [
                'id_hutang' => $idHutang,
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
            $this->hutangBayar->save($data);
            $idHutangBayar = $this->hutangBayar->getInsertID();
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Hutang Bayar ID: ' . $idHutangBayar]);

            // Ambil Data Hutang
            $hutang = $this->model->find($idHutang);
            $idPembelian = $hutang['id_pembelian'];
            $jumlahBayar = (int)$hutang['jumlah_bayar'];
            $sisaHutang = (int)$hutang['sisa_hutang'];
            $dataHutang = [
                'jumlah_bayar' => $nominal + $jumlahBayar,
                'sisa_hutang' => $sisaHutang - $nominal,
            ];
            $this->model->update($idHutang, $dataHutang);

            //Data Cashflow
            $dataKas = [
                'faktur' => $faktur,
                'jenis' => 'Pengeluaran',
                'kategori' => 'Pembelian',
                'tanggal' => date('Y-m-d', strtotime(Time::now())),
                'waktu' => date('H:i:s', strtotime(Time::now())),
                'pemasukan' => 0,
                'pengeluaran' => $nominal,
                'keterangan' => 'Pembayaran Hutang Pembelian: ' . $faktur,
                'id_pembelian' => $idPembelian,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            $this->cashflow->save($dataKas);
            $idCashflow = $this->cashflow->getInsertID();
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $faktur]);

            // Update Hutang Bayar untuk masukkan id_cashflow
            $this->hutangBayar->update($idHutangBayar, ['id_cashflow' => $idCashflow]);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Hutang Bayar ID: ' . $idHutangBayar]);

            // Ambil Sisa Hutang
            $hutang = $this->model->find($idHutang);
            $idsisa = $hutang['id_pembelian'];
            $sisanya = $hutang['sisa_hutang'];
            if ($sisanya == 0) :
                $this->model->update($idHutang, ['status_hutang' => 1]);
                $this->pembelian->update($idsisa, ['status_bayar' => 1]);

                //Ambil Data Item Pembelian buat Update Stok Barang karena Hutang Lunas
                /* $itemBeli = $this->itemBeli->where('id_pembelian', $idPembelian)->findAll();
                foreach ($itemBeli as $row) {
                    $idBrg = $row['id_barang'];
                    $qtyBeli = $row['qty'];

                    $barang = $this->barang->find($idBrg);
                    $stokBrg = $barang['stok'];
                    $stokGd = $barang['stok_gudang'];

                    $updateStok = [
                        'stok' => $stokBrg + $qtyBeli,
                        'stok_gudang' => $stokGd - $qtyBeli
                    ];

                    //Update Stok Barang
                    $this->barang->update($idBrg, $updateStok);

                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Barang Stok: ' . $idBrg]);
                } */
            endif;

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => ['sisa_hutang' => $sisanya],
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
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Hutang: ' . $id]);

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
        $idPembelian = $delete['id_pembelian'];
        $jumlahHutang = $delete['jumlah_hutang'];
        $jumlahBayar = $delete['jumlah_bayar'];
        if ($delete) {
            //Cari Pembelian
            $pembelian = $this->pembelian->find($idPembelian);
            $faktur = $pembelian['faktur'];
            /* $findItemBeli = $this->itemBeli->where('id_pembelian', $idPembelian)->findAll();
            foreach ($findItemBeli as $row) {
                $idBarang = $row['id_barang'];
                $qtyBarang = $row['qty'];

                //Cari data Barang
                $findBarang = $this->barang->where('id_barang', $idBarang)->first();
                $stokBarang = $findBarang['stok'];
                $stokGudang = $findBarang['stok_gudang'];
                $stokUpdate = [
                    'stok' => $stokBarang + $qtyBarang,
                    'stok_gudang' => $stokGudang - $qtyBarang
                ];
                //Update stok Barang
                $this->barang->update($idBarang, $stokUpdate);
            } */

            //Update Data Pembelian
            $this->pembelian->update($idPembelian, ['status_bayar' => 1]);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Pembelian: ' . $idPembelian]);

            //Data Cashflow
            $dataKas = [
                'faktur' => $faktur,
                'jenis' => 'Pengeluaran',
                'kategori' => 'Pembelian',
                'tanggal' => date('Y-m-d', strtotime(Time::now())),
                'waktu' => date('H:i:s', strtotime(Time::now())),
                'pemasukan' => 0,
                'pengeluaran' => $jumlahHutang,
                'keterangan' => 'Pembelian: ' . $faktur,
                'id_pembelian' => $idPembelian,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            //Save Cashflow
            $this->cashflow->save($dataKas);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $faktur]);

            //Hapus Hutang berdasarkan $id
            $this->model->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Hutang: ' . $id]);

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
        $delete = $this->hutangBayar->find($id);
        $idHutang = $delete['id_hutang'];
        $nominal = $delete['nominal'];
        $idCashflow = $delete['id_cashflow'];
        if ($delete) {
            //Cari Data Hutang berdasarkan $idHutang
            $hutang = $this->model->find($idHutang);
            $idPembelian = $hutang['id_pembelian'];
            $jumlahBayar = $hutang['jumlah_bayar'];
            $sisaHutang = $hutang['sisa_hutang'];
            $dataHutang = [
                'jumlah_bayar' => $jumlahBayar - $nominal,
                'sisa_hutang' => $sisaHutang + $nominal,
            ];
            $this->model->update($idHutang, $dataHutang);

            //Cari data Cashflow
            $cash = $this->cashflow->find($idCashflow);
            if ($cash) :
                //Hapus Cash
                $this->cashflow->delete($idCashflow);
                //Save Log
                $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Cashflow: ' . $idCashflow]);
            endif;

            //Ambil Sisa Hutang
            $sisa = $this->model->find($idHutang);
            $sisanya = $sisa['sisa_hutang'];
            $jumlahsisa = $sisa['jumlah_hutang'];
            $statusnya = $sisa['status_hutang'];

            //Update Hutang
            if ($sisanya == $nominal) :
                $this->pembelian->update($idPembelian, ['status_bayar' => 0]);
                $this->model->update($idHutang, ['status_hutang' => 0]);
            endif;

            //Hapus Hutang Bayar berdasarkan $id
            $this->hutangBayar->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Hutang Bayar: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => ['sisa_hutang' => $sisanya],
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
