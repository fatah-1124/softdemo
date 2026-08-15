<?php

namespace App\Modules\Barang\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Media\Models\MediaModel;
use App\Modules\Pembelian\Models\PembelianItemModel;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Penjualan\Models\PenjualanItemModel;
use App\Modules\Kategori\Models\KategoriModel;
use App\Modules\Log\Models\LogModel;
use App\Modules\Toko\Models\TokoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;
use \Milon\Barcode\DNS1D;

class Barang extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = BarangModel::class;
    protected $media;
    protected $jualItem;
    protected $beliItem;
    protected $kategori;
    protected $log;
    protected $toko;

    public function __construct()
    {
        $this->media = new MediaModel();
        $this->jualItem = new PenjualanItemModel();
        $this->beliItem = new PembelianItemModel();
        $this->kategori = new KategoriModel();
        $this->log = new LogModel();
        $this->toko = new TokoModel();
    }

    public function index()
    {
        $input = $this->request->getVar();
        $where = $input['kategori'] ?? "";
        if ($where == "") {
            $data = $this->model->getBarang();
        } else {
            $data = $this->model->getBarang($where);
        }
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "total_page" => $this->model->countBarang()
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
        $data = $this->model->showBarang($id);
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

    public function create()
    {
        $rules = [
            'barcode' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'id_kategori' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama_barang' => [
                'rules'  => 'required|min_length[3]|max_length[255]',
                'errors' => []
            ],
            'satuan_barang' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'satuan_nilai' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'merk' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'harga_beli' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'harga_jual' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'stok' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        //Ambil max id_barang dan kode jual toko
        $query = $this->model->selectMax('id_barang', 'last');
        $hasil = $query->get()->getRowArray();
        $last = $hasil['last'] + 1;
        $noKode = sprintf('%02s', $last);
        $toko = $this->toko->first();
        $kdBarang = $toko['kode_barang'];
        $kodeBarang = $kdBarang . $noKode;

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $idBarang = $json->uuid_barang;
            $hargaJual = (int)$json->harga_jual;
            $diskon = (int)$json->diskon;
            $hitung = $hargaJual - $diskon;
            $persen = $hargaJual - $hitung;
            if ($diskon != 0) {
                $diskonPersen = @($persen / $hargaJual) * 100;
            } else {
                $diskonPersen = 0;
            }
            $idMedia = $json->id_media;
            $data = [
                'uuid_barang' => $idBarang,
                'kode_barang' => $kodeBarang,
                'barcode' => $json->barcode,
                'id_kategori' => $json->id_kategori,
                'sku' => $json->sku,
                'nama_barang' => $json->nama_barang,
                'merk' => $json->merk,
                'harga_beli' => $json->harga_beli,
                'harga_jual' => $hargaJual,
                'harga_member' => 0,
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'satuan_barang' => $json->satuan_barang,
                'satuan_nilai' => $json->satuan_nilai,
                'deskripsi' => $json->deskripsi,
                'stok' => $json->stok,
                'active' => $json->active,
                'stok_min' => $json->stok_min,
                'id_kontak' => $json->id_kontak,
                'expired' => $json->expired,
                'id_toko' => 1,
                'margin' => $json->margin,
                'jumlah_min_grosir' => $json->jumlah_min_grosir,
                'harga_jual_grosir' => $json->harga_jual_grosir
            ];
        } else {
            $idBarang = $this->request->getPost('uuid_barang');
            $diskon = (int)$this->request->getPost('diskon');
            $hargaJual = (int)$this->request->getPost('harga_jual');
            $hitung = $hargaJual - $diskon;
            $persen = $hargaJual - $hitung;
            if ($diskon != 0) {
                $diskonPersen = @($persen / $hargaJual) * 100;
            } else {
                $diskonPersen = 0;
            }
            $idMedia = $this->request->getPost('id_media');
            $data = [
                'uuid_barang' => $idBarang,
                'kode_barang' => $kodeBarang,
                'barcode' => $this->request->getPost('barcode'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'sku' => $this->request->getPost('sku'),
                'nama_barang' => $this->request->getPost('nama_barang'),
                'merk' => $this->request->getPost('merk'),
                'harga_beli' => $this->request->getPost('harga_beli'),
                'harga_jual' => $hargaJual,
                'harga_member' => 0,
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'satuan_barang' => $this->request->getPost('satuan_barang'),
                'satuan_nilai' => $this->request->getPost('satuan_nilai'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'stok' => $this->request->getPost('stok'),
                'active' => $this->request->getPost('active'),
                'stok_min' => $this->request->getPost('stok_min'),
                'id_kontak' => $this->request->getPost('id_kontak'),
                'expired' => $this->request->getPost('expired'),
                'id_toko' => 1,
                'margin' => $this->request->getPost('margin'),
                'jumlah_min_grosir' => $this->request->getPost('jumlah_min_grosir'),
                'harga_jual_grosir' => $this->request->getPost('harga_jual_grosir')
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
            $lastId = $this->model->getInsertID();

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Barang: ' . $idBarang]);

            if ($idMedia != '') :
                $this->media->update($idMedia, ['id_barang' => $lastId]);
            endif;

            $response = [
                'status' => true,
                'message' => lang('App.itemSuccess'),
                'data' => ['url' => base_url('barang')],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'barcode' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'id_kategori' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama_barang' => [
                'rules'  => 'required|min_length[3]|max_length[255]',
                'errors' => []
            ],
            'satuan_barang' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'satuan_nilai' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'merk' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'harga_beli' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'harga_jual' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'stok' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $hargaJual = (int)$json->harga_jual;
            $diskon = (int)$json->diskon;
            $hitung = $hargaJual - $diskon;
            $persen = $hargaJual - $hitung;
            $diskonPersen = @($persen / $hargaJual) * 100;
            //Cek margin harga
            $hitungHarga = $hargaJual - $json->harga_beli;
            if ($json->harga_beli != 0) {
                $margin = @($hitungHarga / $json->harga_beli) * 100;
            } else {
                $margin = $json->margin;
            }
            $data = [
                'barcode' => $json->barcode,
                'id_kategori' => $json->id_kategori,
                'sku' => $json->sku,
                'nama_barang' => $json->nama_barang,
                'merk' => $json->merk,
                'harga_beli' => $json->harga_beli,
                'harga_jual' => $hargaJual,
                'harga_member' => 0,
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'satuan_barang' => $json->satuan_barang,
                'satuan_nilai' => $json->satuan_nilai,
                'deskripsi' =>  $json->deskripsi,
                'stok' => $json->stok,
                'stok_min' => $json->stok_min,
                'id_kontak' => $json->id_kontak,
                'expired' => $json->expired,
                'margin' => $json->margin == 0 ? $margin:$json->margin,
                'jumlah_min_grosir' => $json->jumlah_min_grosir,
                'harga_jual_grosir' => $json->harga_jual_grosir
            ];
        } else {
            $input = $this->request->getRawInput();
            $hargaJual = (int)$input['harga_jual'];
            $diskon = (int)$input['diskon'];
            $hitung = $hargaJual - $diskon;
            $persen = $hargaJual - $hitung;
            $diskonPersen = @($persen / $hargaJual) * 100;
            //Cek margin harga
            $hitungHarga = $hargaJual - $input['harga_beli'];
            if ($input['harga_beli'] != 0) {
                $margin = @($hitungHarga / $input['harga_beli']) * 100;
            } else {
                $margin = $input['margin'];
            }
            $data = [
                'barcode' => $input['barcode'],
                'id_kategori' => $input['id_kategori'],
                'sku' => $input['sku'],
                'nama_barang' => $input['nama_barang'],
                'merk' => $input['merk'],
                'harga_beli' => $input['harga_beli'],
                'harga_jual' => $hargaJual,
                'harga_member' => 0,
                'diskon' => $diskon,
                'diskon_persen' => $diskonPersen,
                'satuan_barang' => $input['satuan_barang'],
                'satuan_nilai' => $input['satuan_nilai'],
                'deskripsi' => $input['deskripsi'],
                'stok' => $input['stok'],
                'stok_min' => $input['stok_min'],
                'id_kontak' => $input['id_kontak'],
                'expired' => $input['expired'],
                'margin' => $input['margin'] == 0 ? $margin:$input['margin'],
                'jumlah_min_grosir' => $this->request->getPost('jumlah_min_grosir'),
                'harga_jual_grosir' => $this->request->getPost('harga_jual_grosir')
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Barang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.ItemUpdated'),
                'data' => ['url' => base_url('barang')],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $cekPenjualan = $this->jualItem->where("id_barang", $id)->findAll();
        $cekPembelian = $this->beliItem->where("id_barang", $id)->findAll();
        if (empty($cekPenjualan) && empty($cekPembelian)) {
            // Delete media
            $qmedia = $this->media->where(['id_barang' => $id])->first();
            if ($qmedia) :
                $idmedia = $qmedia['id_media'];
                $foto = $qmedia['media_path'];
                unlink($foto);
                $this->media->delete($idmedia);
            endif;

            $this->model->delete($id);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Barang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.ItemDeleted'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailedSold'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function deleteMultiple()
    {
        $input = $this->request->getVar('data');
        $data = json_decode($input, true);
        $countData = count($data);
        $count = 0;

        foreach ($data as $data) {
            $id = $data['id_barang'];
            $cekPenjualan = $this->jualItem->where("id_barang", $id)->findAll();
            $cekPembelian = $this->beliItem->where("id_barang", $id)->findAll();
            if (empty($cekPenjualan) && empty($cekPembelian)) :
                $qmedia = $this->media->where(['id_barang' => $id])->first();
                if ($qmedia != null) :
                    $foto = $qmedia['media_path'];
                    $idmedia = $qmedia['id_media'];
                    unlink($foto);
                    $this->media->delete($idmedia);
                endif;
                $this->model->delete($id);
                $count = $this->model->affectedRows();
            endif;
        }

        //Save Log
        $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Barang: Multiple']);

        $response = [
            'status' => true,
            'message' => lang('App.delSuccess') . '. ' . $count . ' item/s deleted succesfully. ' . ($countData - $count) . ' item/s cannot be deleted',
            'data' => [],
        ];
        return $this->respond($response, 200);
    }

    public function setHargaBeli($id = NULL)
    {
        $barang = $this->model->find($id);
        $hargaJual = $barang['harga_jual'];

        $rules = [
            'harga_beli' => [
                'rules'  => "less_than[$hargaJual]",
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $hargaBeli = $json->harga_beli;
            $data = [
                'harga_beli' => $hargaBeli,
            ];
        } else {
            $input = $this->request->getRawInput();
            $hargaBeli = $input['harga_beli'];
            $data = [
                'harga_beli' => $hargaBeli,
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update harga_beli Barang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.ItemUpdated'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setHargaJual($id = NULL)
    {
        $barang = $this->model->find($id);
        $hargaBeli = $barang['harga_beli'];

        $rules = [
            'harga_jual' => [
                'rules'  => "greater_than[$hargaBeli]",
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $hargaJual = $json->harga_jual;
            $data = [
                'harga_jual' => $hargaJual,
            ];
        } else {
            $input = $this->request->getRawInput();
            $hargaJual = $input['harga_jual'];
            $data = [
                'harga_jual' => $hargaJual,
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update harga_jual Barang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.ItemUpdated'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function setStok($id = NULL)
    {
        $rules = [
            'jenis' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'value_transfer' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        $barang = $this->model->find($id);
        $barangStok = $barang['stok'];
        $barangStokGudang = $barang['stok_gudang'];
        $barangActive = $barang['active'];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $stok = $json->stok;
            $stokGd = $json->stok_gudang;
            $jenis = $json->jenis;
            $transfer = $json->value_transfer;
        } else {
            $input = $this->request->getRawInput();
            $stok = $input['stok'];
            $stokGd = $input['stok_gudang'];
            $jenis = $input['jenis'];
            $transfer = $input['value_transfer'];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            if ($barang) {
                if ($jenis == 'in') {
                    if ($stokGd == 0) :
                        $response = [
                            'status' => false,
                            'message' => lang('App.updFailed') . ' ' . lang('App.stock') . ' 0',
                            'data' => []
                        ];
                        return $this->respond($response, 200);
                    endif;

                    if ($stok == 0) {
                        $data = ['stok' => $stok + $transfer, 'stok_gudang' => $stokGd - $transfer, 'active' => 0];
                    } else {
                        $data = [
                            'stok' => $stok + $transfer,
                            'stok_gudang' => $stokGd - $transfer
                        ];
                    }
                } else if ($jenis == 'out') {
                    if ($stok == 0) :
                        $response = [
                            'status' => false,
                            'message' => lang('App.updFailed') . ' ' . lang('App.stock') . ' 0',
                            'data' => []
                        ];
                        return $this->respond($response, 200);
                    endif;

                    $data = [
                        'stok' => $stok - $transfer,
                        'stok_gudang' => $stokGd + $transfer
                    ];
                } else if ($jenis == 'wh') {
                    $data = [
                        'stok_gudang' => $stokGd + $transfer
                    ];
                } else {
                    if ($stok == 0) {
                        $data = [
                            'stok' => $stok,
                            'stok_gudang' => $stokGd,
                            'active' => 0
                        ];
                    } else if ($barangActive == 0 && $stok > 0) {
                        $data = [
                            'stok' => $stok,
                            'stok_gudang' => $stokGd,
                            'active' => 1
                        ];
                    } else {
                        $data = [
                            'stok' => $stok,
                            'stok_gudang' => $stokGd
                        ];
                    }
                }
                // Update
                $this->model->update($id, $data);

                //Save Log
                $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update stok Barang: ' . $id]);

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
    }

    public function setAktif($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $active = $json->active;
            $data = [
                'active' => $active,
            ];
        } else {
            $input = $this->request->getRawInput();
            $active = $input['active'];
            $data = [
                'active' => $active,
            ];
        }

        if ($data > 0) {
            $qStok = $this->model->find($id);
            $cStok = $qStok['stok'];

            if ($cStok <= '0') {
                $this->model->update($id, ['active' => $active]);
            } else {
                $this->model->update($id, $data);
            }

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update active Barang: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.ItemUpdated'),
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

    public function getBarangTerbaru()
    {
        $input = $this->request->getVar();
        $page = $input['page'];
        $limit = $input['limit'];
        $data = $this->model->getBarangTerbaru($page, $limit);
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "per_page" => $limit,
                "total_page" => $this->model->countBarang()
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

    public function getBarangKasir()
    {
        $input = $this->request->getVar();
        $data = $this->model->getBarangKasir();
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "total_page" => $this->model->countBarang('active', 1)
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

    public function cariBarang()
    {
        $input = $this->request->getVar();
        $query = $input['query'];
        $data = $this->model->searchBarang($query);
        if (!empty($data)) {
            $response = [
                'status' => true,
                'message' => lang('App.getSuccess'),
                'data' => $data
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

    public function scanBarang()
    {
        $input = $this->request->getVar();
        $query = $input['query'];
        $data = $this->model->scanBarang($query);
        if ($data) {
            $response = [
                'status' => true,
                'message' => lang('App.getSuccess'),
                'data' => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.outOfStock'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function barangHabis()
    {
        $input = $this->request->getVar();
        $where = $input['kategori'] ?? "";
        if ($where == "") {
            $data = $this->model->getBarangHabis();
        } else {
            $data = $this->model->getBarangHabis($where);
        }
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "total_page" => $this->model->countBarang('stok', 0)
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

    public function barangNonaktif()
    {
        $input = $this->request->getVar();
        $where = $input['kategori'] ?? "";
        if ($where == "") {
            $data = $this->model->getBarangNonaktif();
        } else {
            $data = $this->model->getBarangNonaktif($where);
        }
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "total_page" => $this->model->countBarang('active', 0)
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

    public function jmlSemuaBarang()
    {
        return $this->respond([
            "status" => true,
            "message" => lang('App.getSuccess'),
            "data" => $this->model->countBarang(),
        ], 200);
    }

    public function jmlStokHabis()
    {
        return $this->respond([
            "status" => true,
            "message" => lang('App.getSuccess'),
            "data" => $this->model->countBarang('stok', 0),
        ], 200);
    }

    public function jmlNonaktif()
    {
        return $this->respond([
            "status" => true,
            "message" => lang('App.getSuccess'),
            "data" => $this->model->countBarang('active', 0),
        ], 200);
    }

    public function getBarangBeliVendor($id)
    {
        $data = $this->model->beliBarangVendor($id);
        $response = [
            "status" => true,
            "message" => lang('App.getSuccess'),
            "data" => $data,
        ];
        return $this->respond($response, 200);
    }

    public function findBarang()
    {
        $input = $this->request->getVar();
        $query = $input['query'];
        $data = $this->model->findBarang($query);
        if (!empty($data)) {
            $response = [
                'status' => true,
                'message' => lang('App.getSuccess'),
                'data' => $data
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

    public function barcodeMultiple()
    {
        $input = $this->request->getVar('data');
        $data = json_decode($input, true);

        foreach ($data as $row) {
            $barcode = $row['barcode'];
            if ($barcode == "" || $barcode == null) :
                $count = array_count_values(array_column($data, 'barcode'))[$barcode];
                $response = [
                    'status' => false,
                    'message' => 'Ada '. $count .' Barang yang tidak memiliki Barcode, mohon un-check pada data barang tersebut',
                    'data' => [],
                ];
                return $this->respond($response, 200);
            endif;
        }

        helper('text');
        $toko = $this->toko->first();
        $barcode = new DNS1D();
        $barcode->setStorPath(WRITEPATH . 'cache/');
        $data =  [
            "namaToko" => $toko['nama_toko'],
            'data' => $data,
            'jumlahData' => count($data),
            'jumlah' => 27,
            'barcode' => $barcode,
            'tipe' => 'C128'
        ];

        $html = view("App\Modules\Barang\Views/barcode_multiple", $data);

        $file = FCPATH . '/files/barcode_multiple.html';
        file_put_contents($file, $html);
        $fileHTML = base_url('/files/barcode_multiple.html');

        if (file_exists($file)) {
            $response = [
                'status' => true,
                'message' => 'Success Print Barcode',
                'data' => ['url' => $fileHTML . '?=' . random_string(8)],
            ];
            return $this->respond($response, 200);
        }
    }

    public function labelRackMultiple()
    {
        $input = $this->request->getVar('data');
        $data = json_decode($input, true);

        helper('text');
        $toko = $this->toko->first();
        $barcode = new DNS1D();
        $barcode->setStorPath(WRITEPATH . 'cache/');
        $data =  [
            "namaToko" => $toko['nama_toko'],
            'data' => $data,
            'jumlahData' => count($data),
            'jumlah' => 18,
            'barcode' => $barcode,
            'tipe' => 'C128'
        ];

        $html = view("App\Modules\Barang\Views/label_multiple", $data);

        $file = FCPATH . '/files/label_multiple.html';
        file_put_contents($file, $html);
        $fileHTML = base_url('/files/label_multiple.html');

        if (file_exists($file)) {
            $response = [
                'status' => true,
                'message' => 'Success Print Label Rack',
                'data' => ['url' => $fileHTML . '?=' . random_string(8)],
            ];
            return $this->respond($response, 200);
        }
    }

    public function getMerk()
    {
        $data = $this->model->select('merk')->notLike('merk', 'Tidak ada merk')->groupBy('merk')->orderBy('merk', 'RANDOM')->findAll(10);

        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess') . ' Merk (' . count($data) . ')',
                "data" => $data,
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData') . ': Merk (0)',
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }
}
