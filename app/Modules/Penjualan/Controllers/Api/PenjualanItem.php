<?php

namespace App\Modules\Penjualan\Controllers\Api;

use Exception;
use App\Controllers\BaseControllerApi;
use App\Libraries\Settings;
use App\Modules\Penjualan\Models\PenjualanModel;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Penjualan\Models\PenjualanItemModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Cashflow\Models\CashflowModel;
use App\Modules\Pajak\Models\PajakModel;
use App\Modules\Piutang\Models\PiutangModel;
use App\Modules\Log\Models\LogModel;
use App\Modules\Kontak\Models\KontakModel;
use CodeIgniter\I18n\Time;

class PenjualanItem extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = PenjualanItemModel::class;
    protected $setting;
    protected $barang;
    protected $jual;
    protected $toko;
    protected $cashflow;
    protected $pajak;
    protected $piutang;
    protected $log;
    protected $kontak;

    public function __construct()
    {
        $this->setting = new Settings();
        $this->barang = new BarangModel();
        $this->jual = new PenjualanModel();
        $this->toko = new TokoModel();
        $this->cashflow = new CashflowModel();
        $this->pajak = new PajakModel();
        $this->piutang = new PiutangModel();
        $this->log = new LogModel();
        $this->kontak = new KontakModel();
        helper('tglindo');
    }

    public function index()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'] ?? "";
        $end = $input['tgl_end'] ?? "";
        if ($start == "" && $end == "") {
            $data = $this->model->getPenjualan();
        } else {
            $data = $this->model->getPenjualan($start, $end);
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
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->getPenjualanById($id)], 200);
    }

    public function create()
    {
        $toko = $this->toko->first();

        $rules = [
            'id_barang' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();

            $id_barang = $json->id_barang;
            $id_penjualan = $json->id_penjualan;
            $qty = $json->qty;

            $penjualan = $this->jual->find($id_penjualan);
            $kontak = $this->kontak->find($penjualan['id_kontak']);
            $group = $kontak['grup'];

            //cari data barang/barangnya sesuai id_barang
            $data = $this->barang->where(['id_barang' => $id_barang])->first();
            $beli = $data['harga_beli'];
            $minGrosir = $data['jumlah_min_grosir'];
            $jualGrosir = $data['harga_jual_grosir'];
            if ($group == 'member') {
                $jual = $data['harga_member'];
            } else {
                $jual = $data['harga_jual'];
            }
            $satuan = $data['satuan_barang'];
            $diskon = $data['diskon'];
            $diskonPersen = $data['diskon_persen'];
            $hpp = $beli * $qty;
            if ($toko['include_ppn'] == 1) {
                // PPN included in price - correct calculation
                $hargaSetelahDiskon = ($jualGrosir > 0 && $qty >= $minGrosir)
                    ? ((int)$jualGrosir - (int)$diskon) * $qty
                    : ((int)$jual - (int)$diskon) * $qty;
                $DPP = $hargaSetelahDiskon / (1 + $toko['PPN'] / 100);
                $pajak = $hargaSetelahDiskon - $DPP;
                $jumlah = $DPP;
            } else {
                $ppn = ($toko['PPN'] / 100);
                if ($jualGrosir > 0 && $qty >= $minGrosir) {
                    $jumlah = ((int)$jualGrosir - (int)$diskon) * $qty;
                } else {
                    $jumlah = ((int)$jual - (int)$diskon) * $qty;
                }
                $pajak = $jumlah * $ppn;
            }
            $data = [
                'id_barang' => $id_barang,
                'id_penjualan' => $id_penjualan,
                'harga_beli' => $beli,
                'harga_jual' => ($group == 'member' ? $jual : $json->harga_jual),
                'harga_jual_grosir' => $data['harga_jual_grosir'],
                'jumlah_min_grosir' => $data['jumlah_min_grosir'],
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'stok' => $json->stok,
                'qty' => $qty,
                'satuan' => $satuan,
                'hpp' => $hpp,
                'jumlah' => $jumlah,
                'ppn' => $pajak,
                'total_laba' => $jumlah - $hpp,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ];
        } else {
            $id_barang = $this->request->getPost('id_barang');
            $id_penjualan = $this->request->getPost('id_penjualan');
            $qty = $this->request->getPost('qty');

            $penjualan = $this->jual->find($id_penjualan);
            $kontak = $this->kontak->find($penjualan['id_kontak']);
            $group = $kontak['grup'];

            //cari data barang/barangnya sesuai id_barang
            $data = $this->barang->where(['id_barang' => $id_barang])->first();
            $beli = $data['harga_beli'];
            $minGrosir = $data['jumlah_min_grosir'];
            $jualGrosir = $data['harga_jual_grosir'];
            if ($group == 'member') {
                $jual = $data['harga_member'];
            } else {
                $jual = $data['harga_jual'];
            }
            $satuan = $data['satuan_barang'];
            $diskon = $data['diskon'];
            $diskonPersen = $data['diskon_persen'];
            $hpp = $beli * $qty;
            if ($toko['include_ppn'] == 1) {
                // PPN included in price - correct calculation
                $hargaSetelahDiskon = ($jualGrosir > 0 && $qty >= $minGrosir)
                    ? ((int)$jualGrosir - (int)$diskon) * $qty
                    : ((int)$jual - (int)$diskon) * $qty;
                $DPP = $hargaSetelahDiskon / (1 + $toko['PPN'] / 100);
                $pajak = $hargaSetelahDiskon - $DPP;
                $jumlah = $DPP;
            } else {
                $ppn = ($toko['PPN'] / 100);
                if ($jualGrosir > 0 && $qty >= $minGrosir) {
                    $jumlah = ((int)$jualGrosir - (int)$diskon) * $qty;
                } else {
                    $jumlah = ((int)$jual - (int)$diskon) * $qty;
                }
                $pajak = $jumlah * $ppn;
            }
            $data = [
                'id_barang' => $id_barang,
                'id_penjualan' => $id_penjualan,
                'harga_beli' => $beli,
                'harga_jual' => ($group == 'member' ? $jual : $this->request->getPost('harga_jual')),
                'harga_jual_grosir' => $data['harga_jual_grosir'],
                'jumlah_min_grosir' => $data['jumlah_min_grosir'],
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'stok' => $this->request->getPost('stok'),
                'qty' => $qty,
                'satuan' => $satuan,
                'hpp' => $hpp,
                'jumlah' => $jumlah,
                'ppn' => $pajak,
                'total_laba' => $jumlah - $hpp,
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
            //cari barang/barangnya apakah sudah ada di keranjang
            $cari_keranjang = $this->model->where(['id_barang' => $id_barang, 'id_penjualan' => $id_penjualan])->first();

            $barang = $this->barang->where(['id_barang' => $id_barang])->first();
            $minGrosir = $barang['jumlah_min_grosir'];
            $jualGrosir = $barang['harga_jual_grosir'];

            if ($cari_keranjang) {
                $id_keranjang = $cari_keranjang['id_itempenjualan'];
                $beli = $cari_keranjang['harga_beli'];
                $jual = $cari_keranjang['harga_jual'];
                $diskon = $cari_keranjang['diskon'];
                $diskonPersen = $cari_keranjang['diskon_persen'];
                $qty = $cari_keranjang['qty'] + 1;
                $hpp = $beli * $qty;
                if ($toko['include_ppn'] == 1) {
                    // PPN included in price - correct calculation
                    $hargaSetelahDiskon = ($jualGrosir > 0 && $qty >= $minGrosir)
                        ? ((int)$jualGrosir - (int)$diskon) * $qty
                        : ((int)$jual - (int)$diskon) * $qty;
                    $DPP = $hargaSetelahDiskon / (1 + $toko['PPN'] / 100);
                    $pajak = $hargaSetelahDiskon - $DPP;
                    $jumlah = $DPP;
                } else {
                    if ($qty >= $minGrosir) {
                        $jumlah = ((int)$jualGrosir - (int)$diskon) * $qty;
                    } else {
                        $jumlah = ((int)$jual - (int)$diskon) * $qty;
                    }
                    $ppn = ($toko['PPN'] / 100);
                    $pajak = $jumlah * $ppn;
                }
                $update = [
                    'qty' => $qty,
                    'hpp' => $hpp,
                    'jumlah' => $jumlah,
                    'ppn' => $pajak,
                    'total_laba' => $jumlah - $hpp,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $id_barang = $cari_keranjang['id_barang'];
                $qty_barang = $cari_keranjang['qty'];
                $stok = $barang['stok'];

                if ($qty_barang >= $stok) {
                    $response = [
                        'status' => false,
                        'message' => lang('App.stockLess'),
                        'data' => [],
                    ];
                    return $this->respond($response, 200);
                } else {
                    //lalu update qty nya
                    $this->model->update($id_keranjang, $update);
                }
            } else {
                //simpan barang/barang yang belum ada di keranjang
                $this->model->save($data);
            }

            $response = [
                'status' => true,
                'message' => lang('App.itemSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function getItem($id = null)
    {
        return $this->respond(["status" => true, "message" => lang("App.getSuccess"), "data" => $this->model->findNota($id)], 200);
    }

    public function updateItem($id = NULL)
    {
        $toko = $this->toko->first();

        $input = $this->getRequestInput();
        $idBarang = $input['id_barang'];
        $qty = $input['qty'];
        $hargaJual = $input['harga_jual'];

        // cari data barang
        $barang = $this->barang->where(['id_barang' => $idBarang])->first();
        $minGrosir = $barang['jumlah_min_grosir'];
        $jualGrosir = $barang['harga_jual_grosir'];

        if ($barang['stok'] >= $qty) {
            $beli = $barang['harga_beli'];
            $jual = $hargaJual;
            $diskon = $barang['diskon'];
            $diskonPersen = $barang['diskon_persen'];
            $hpp = $beli * $qty;
            if ($toko['include_ppn'] == 1) {
                // PPN included in price - correct calculation
                $hargaSetelahDiskon = ($jualGrosir > 0 && $qty >= $minGrosir)
                    ? ((int)$jualGrosir - (int)$diskon) * $qty
                    : ((int)$jual - (int)$diskon) * $qty;
                $DPP = $hargaSetelahDiskon / (1 + $toko['PPN'] / 100);
                $pajak = $hargaSetelahDiskon - $DPP;
                $jumlah = $DPP;
            } else {
                $ppn = ($toko['PPN'] / 100);
                if ($jualGrosir > 0 && $qty >= $minGrosir) {
                    $jumlah = ((int)$jualGrosir - (int)$diskon) * $qty;
                } else {
                    $jumlah = ((int)$jual - (int)$diskon) * $qty;
                }
                $pajak = $jumlah * $ppn;
            }
            $data = [
                'harga_jual' => $hargaJual,
                'qty' => $qty,
                'hpp' => $hpp,
                'jumlah' => $jumlah,
                'ppn' => $pajak,
                'total_laba' => $jumlah - $hpp,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->model->update($id, $data);
            /* var_dump($this->model->getLastQuery()->getQuery());
            die; */
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.stockLess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $delete = $this->model->find($id);

        if ($delete) {
            //Cari data barang/barangnya
            $idBarang = $delete['id_barang'];
            $qty = $delete['qty'];
            $barang = $this->barang->where('id_barang', $idBarang)->first();
            $stok = $barang['stok'];
            $dataStok = [
                'stok' => $stok + $qty,
            ];
            //Update stok barang/barangnya
            $this->barang->update($idBarang, $dataStok);
            //Hapus penjualan
            $this->model->delete($id);
            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Penjualan Item: ' . $id]);

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
