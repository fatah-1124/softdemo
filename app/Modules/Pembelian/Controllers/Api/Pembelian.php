<?php

namespace App\Modules\Pembelian\Controllers\Api;

use Exception;
use App\Controllers\BaseControllerApi;
use App\Libraries\Settings;
use App\Modules\Hutang\Models\HutangModel;
use App\Modules\Pembelian\Models\PembelianModel;
use App\Modules\Pembelian\Models\PembelianItemModel;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\I18n\Time;

class Pembelian extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PembelianModel::class;
    protected $setting;
    protected $barang;
    protected $itemBeli;
    protected $toko;
    protected $cashflow;
    protected $hutang;
    protected $log;

    public function __construct()
    {
        $this->setting = new Settings();
        $this->barang = new BarangModel();
        $this->itemBeli = new PembelianItemModel();
        $this->toko = new TokoModel();
        $this->cashflow = new CashflowModel();
        $this->hutang = new HutangModel();
        $this->log = new LogModel();
        helper('text');
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        if ($start == "" && $end == "") {
            $data = $this->model->getPembelian();
        } else {
            $data = $this->model->getPembelian($start, $end);
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
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->find($id)], 200);
    }

    public function create()
    {
        $rules = [
            'id_kontak' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'tanggal' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'total' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $tanggal = $json->tanggal;
            $jatuhTempo = $json->jatuh_tempo;
            $subTotal = $json->subtotal;
            $biaya = $json->biaya;
            $total = $json->total;
            $catatan = $json->catatan;
            $idKontak = $json->id_kontak;
            $statusBayar = $json->status_bayar;
            if ($statusBayar == 0) {
                $rules = [
                    'jatuh_tempo' => [
                        'rules'  => 'required',
                        'errors' => []
                    ],
                ];
            } else {
                $jatuhTempo = date('Y-m-d');
            }
        } else {
            $tanggal = $this->request->getPost('tanggal');
            $jatuhTempo = $this->request->getPost('jatuh_tempo');
            $subTotal = $this->request->getPost('subtotal');
            $biaya = $this->request->getPost('biaya');
            $total = $this->request->getPost('total');
            $catatan = $this->request->getPost('catatan');
            $idKontak = $this->request->getPost('id_kontak');
            $statusBayar = $this->request->getPost('status_bayar');
            if ($statusBayar == 0) {
                $rules = [
                    'jatuh_tempo' => [
                        'rules'  => 'required',
                        'errors' => []
                    ],
                ];
            } else {
                $jatuhTempo = date('Y-m-d');
            }
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $input = $this->request->getVar('data');

            foreach ($input as $value) {
                $id_barang[] = $value[0];
                $harga_beli[] = $value[1];
                $stok[] = $value[2];
                $jumlah[] = $value[3];
                $satuan[] = $value[4];
                $harga_jual[] = $value[5];
            }
            $total_barang = count($id_barang);

            //Ambil kode jual toko
            $toko = $this->toko->first();
            $kdBeli = $toko['kode_beli'];
            $kdJualTahun = $toko['kode_jual_tahun'];
            //Ambil timestamp
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
                // Hitung transaksi per hari
                $query = $this->model
                    ->select('DATE(created_at) as date_val, COUNT(*) as total')
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->groupBy('DATE(created_at)');
                $hasil = $query->get()->getRowArray();
                $last  = empty($hasil) ? 1 : $hasil['total'] + 1;
                $lastKode = sprintf('%02s', $last);
                // contoh tanpa id_toko
                $noBeli = $kdBeli . date('dmy') . $lastKode;
            } else {
                // Hitung transaksi per bulan
                $query = $this->model
                    ->select('YEAR(created_at) as year_val, MONTH(created_at) as month_val, COUNT(*) as total')
                    ->where('YEAR(created_at)', date('Y'))
                    ->where('MONTH(created_at)', date('m'))
                    ->groupBy('YEAR(created_at), MONTH(created_at)');
                $hasil = $query->get()->getRowArray();
                $last  = empty($hasil) ? 1 : $hasil['total'] + 1;
                $lastKode = sprintf('%02s', $last);
                $noBeli = $kdBeli . date('my') . $lastKode;
            }

            $data = [
                'faktur' => $noBeli,
                'id_kontak' => $idKontak,
                'tanggal' => $tanggal,
                'jatuh_tempo' => $jatuhTempo,
                'jumlah' => $total_barang,
                'subtotal' => $subTotal,
                'biaya' => $biaya,
                'total' => $total,
                'catatan' => $catatan,
                'status_bayar' => $statusBayar,
                'id_login' => session()->get('id'),
                'id_toko' => 1
            ];
            $this->model->save($data);
            $idBeli = $this->model->getInsertID();

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Pembelian: ' . $idBeli]);

            $arrItem = array();
            foreach ($input as $key => $value) {
                $id_barang = $value[0];
                $harga_beli = $value[1];
                $stok = $value[2];
                $qty = $value[3];
                $satuan = $value[4];
                $harga_jual = $value[5];

                $item = array(
                    'id_barang' => $id_barang,
                    'id_pembelian' => $idBeli,
                    'harga_beli' => $harga_beli,
                    'harga_jual' => $harga_jual,
                    'qty' => $qty,
                    'satuan' => $satuan,
                    'jumlah' => $harga_beli * $qty,
                );
                array_push($arrItem, $item);
            }
            $dataItem = $arrItem;
            $this->itemBeli->insertBatch($dataItem);

            if ($statusBayar == 1) {
                $arrStok = array();
                foreach ($input as $key => $value) {
                    $id_barang = $value[0];
                    $harga_beli = $value[1];
                    $stok = $value[2];
                    $qty = $value[3];
                    $satuan = $value[4];
                    $harga_jual = $value[5];

                    $stock = array(
                        'id_barang' => $id_barang,
                        'stok' => $stok + $qty,
                    );

                    array_push($arrStok, $stock);

                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Barang Stok: ' . $id_barang]);
                }
                $dataStok = $arrStok;
                $this->barang->updateBatch($dataStok, 'id_barang');

                //Data Cashflow
                $dataKas = [
                    'faktur' => $noBeli,
                    'jenis' => 'Pengeluaran',
                    'kategori' => 'Pembelian',
                    'tanggal' => date('Y-m-d', strtotime(Time::now())),
                    'waktu' => date('H:i:s', strtotime(Time::now())),
                    'pemasukan' => 0,
                    'pengeluaran' => $total,
                    'keterangan' => 'Pembelian: ' . $noBeli,
                    'id_pembelian' => $idBeli,
                    'id_toko' => 1,
                    'id_login' => session()->get('id'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null
                ];
                //Save Cashflow
                $this->cashflow->save($dataKas);
            } else if ($statusBayar == 0) {
                // Hutang
                $arrStok = array();
                foreach ($input as $key => $value) {
                    $id_barang = $value[0];
                    $harga_beli = $value[1];
                    $stok = $value[2];
                    $qty = $value[3];
                    $satuan = $value[4];
                    $harga_jual = $value[5];

                    // Cek Stok Gudang
                    $cekStok = $this->barang->find($id_barang);
                    $stok = $cekStok['stok'];

                    $stock = array(
                        'id_barang' => $id_barang,
                        'stok' => $stok + $qty,
                    );

                    array_push($arrStok, $stock);

                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Barang Stok: ' . $id_barang]);
                }
                $dataStok = $arrStok;
                $this->barang->updateBatch($dataStok, 'id_barang');

                // Status Bayar 0 Masukkan ke Hutang
                $dataHutang = [
                    'id_pembelian' =>  $idBeli,
                    'tanggal' => $tanggal,
                    'jatuh_tempo' => $jatuhTempo,
                    'jumlah_hutang' => $total,
                    'jumlah_bayar' => 0,
                    'sisa_hutang' => $total,
                    'status_hutang' => 0,
                    'id_login' => session()->get('id'),
                    'id_toko' => 1
                ];
                $this->hutang->save($dataHutang);
            }

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => ['url' => base_url('pembelian')],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'status_bayar' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $statusBayar = $json->status_bayar;
            $data = [
                'status_bayar' => $statusBayar,
            ];
        } else {
            $input = $this->request->getRawInput();
            $statusBayar = $input['status_bayar'];
            $data = [
                'status_bayar' => $statusBayar,
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
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update status_bayar Pembelian: ' . $id]);

            /* $item = $this->itemBeli->where('id_pembelian', $id)->findAll();
            $arrStok = array();
            foreach ($item as $key => $value) {
                $id_barang = $value['id_barang'];
                $qty = $value['qty'];

                $barang = $this->barang->where('id_barang', $id_barang)->first();
                $stok = $barang['stok'];

                $stock = array(
                    'id_barang' => $id_barang,
                    'stok' => $stok + $qty,
                );

                array_push($arrStok, $stock);
            }
            $dataStok = $arrStok;
            $this->barang->updateBatch($dataStok, 'id_barang'); */

            /*  //Ambil data Beli
            $beli = $this->model->where('id_pembelian', $id)->first();
            $noBeli = $beli['faktur'];
            $total = $beli['total'];

            //Data Cashflow
            $dataKas = [
                'faktur' => $noBeli,
                'jenis' => 'Pengeluaran',
                'kategori' => 'Pembelian',
                'tanggal' => date('Y-m-d', strtotime(Time::now())),
                'waktu' => date('H:i:s', strtotime(Time::now())),
                'pemasukan' => 0,
                'pengeluaran' => $total,
                'keterangan' => 'Pembelian: ' . $noBeli,
                'id_pembelian' => $idBeli,
                'id_toko' => 1,
                'id_login' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
            //Save Kas
            $this->cashflow->save($dataKas);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Cashflow: ' . $noBeli]); */

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

        //Cek data Hutang dulu karena akan terkena Foreign key cek 'restrict'
        $cekHutang = $this->hutang->where(['id_pembelian' => $id])->findAll();
        if ($cekHutang) :
            $response = [
                'status' => true,
                'message' => lang('App.delFailedHutang'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        if (!$cekHutang) {
            //Cari data item
            $item = $this->itemBeli->where('id_pembelian', $id)->findAll();
            foreach ($item as $row) {
                $idItemBeli = $row['id_itempembelian'];
                $idBarang = $row['id_barang'];
                $qty = $row['qty'];

                //Cari data barang/barangnya
                $barang = $this->barang->where('id_barang', $idBarang)->first();
                $stok = $barang['stok'];
                $dataStok = [
                    'stok' => $stok - $qty,
                ];
                //Update stok barang/barangnya
                $this->barang->update($idBarang, $dataStok);

                //Hapus item
                $this->itemBeli->delete($idItemBeli);
            }

            //Cari data Cashflow
            $cash = $this->cashflow->where('faktur', $faktur)->findAll();
            if ($cash) :
                foreach ($cash as $row) {
                    $idCashflow = $row['id_cashflow'];
                    //Hapus Cashflow
                    $this->cashflow->delete($idCashflow);
                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Cashflow: ' . $idCashflow]);
                }
            endif;

            //Hapus pembelian
            $this->model->delete($id);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Pembelian: ' . $id]);

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

    public function item($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->itemBeli->findItem($id)], 200);
    }
}
