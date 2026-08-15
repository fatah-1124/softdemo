<?php

namespace  App\Modules\Excel\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Kategori\Models\KategoriModel;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Kontak\Models\KontakModel;
use App\Modules\Satuan\Models\SatuanModel;
use App\Modules\Toko\Models\TokoModel;
use App\Modules\Log\Models\LogModel;
use Ramsey\Uuid\Uuid;
use ShortUUID\ShortUUID;

class Excel extends BaseController
{
    protected $setting;
    protected $barang;
    protected $kategori;
    protected $satuan;
    protected $log;
    protected $toko;
    protected $kontak;

    public function __construct()
    {
        //memanggil Model
        $this->setting = new Settings();
        $this->barang = new BarangModel();
        $this->kategori = new KategoriModel();
        $this->satuan = new SatuanModel();
        $this->log = new LogModel();
        $this->toko = new TokoModel();
        $this->kontak = new KontakModel();
    }


    public function import()
    {
        return view('App\Modules\Excel\Views/import', [
            'title' => 'Import Data Barang Excel'
        ]);
    }

    public function saveExcel()
    {
        $rules = [
            'fileexcel' => [
                'rules'  => 'uploaded[fileexcel]|ext_in[fileexcel,xlsx,xls,csv]',
                'errors' => []
            ],
        ];

        $ignoreName = $this->request->getPost('ignorename');
        $file_excel = $this->request->getFile('fileexcel');

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput();
        } else {
            $ext = $file_excel->getClientExtension();
            if ($ext == 'xls') {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $render->load($file_excel);

            $data = $spreadsheet->getActiveSheet()->toArray();

            foreach ($data as $x => $row) {
                if ($x == 0) {
                    continue;
                }
                $barcode = $row[0];
                $namaBarang = $row[1];
                $merk = $row[2];
                $hargaBeli = $row[3];
                $hargaJual = $row[4];
                $diskon = $row[5];
                $kategori = $row[6];
                $satuan = $row[7];
                $satuanNilai = $row[8];
                $deskripsi = $row[9];
                $stok = $row[10];
                $stokMin = $row[11];
                $supplier = $row[12];
                $expired = $row[13];
                $sku = $row[14];

                if ($namaBarang == null) :
                    session()->setFlashdata('error', 'Field nama_barang is Required. Nama Barang harus diisi');
                    return redirect()->back()->withInput();
                endif;
                if ($merk == null) :
                    session()->setFlashdata('error', 'Field merk is Required. Merk Barang harus diisi');
                    return redirect()->back()->withInput();
                endif;
                if ($satuan == null) :
                    session()->setFlashdata('error', 'Field satuan_barang is Required. Satuan Barang harus diisi');
                    return redirect()->back()->withInput();
                endif;
                if ($kategori == null) :
                    session()->setFlashdata('error', 'Field id_kategori is Required. Kategori Barang harus diisi');
                    return redirect()->back()->withInput();
                endif;

                $cekKategori = $this->kategori->where('nama_kategori', $kategori)->first();
                if ($cekKategori) {
                    $idKategori = $cekKategori['id_kategori'];
                } else {
                    $this->kategori->save(['nama_kategori' => $kategori]);
                    $idKategori = $this->kategori->getInsertID();
                }

                $cekSatuan = $this->satuan->where('nama_satuan', $satuan)->first();
                if ($cekSatuan) {
                    $namaSatuan = $cekSatuan['nama_satuan'];
                    $nilaiSatuan = $cekSatuan['nilai_satuan'];
                } else {
                    $this->satuan->save(['nama_satuan' => $satuan, 'nilai_satuan' => $satuanNilai]);
                    $idSatuan = $this->satuan->getInsertID();
                    $qSatuan = $this->satuan->where('id_satuan', $idSatuan)->first();
                    $namaSatuan = $qSatuan['nama_satuan'];
                    $nilaiSatuan = $qSatuan['nilai_satuan'];
                }

                //Cek diskon
                $hitung = $hargaJual - $diskon;
                $persen = $hargaJual - $hitung;
                if ($diskon != 0) {
                    $diskonPersen = @($persen / $hargaJual) * 100;
                } else {
                    $diskonPersen = 0;
                }

                if ($supplier != '') {
                    // memisahkan string menjadi array
                    $data = explode("-" , $supplier);
                    $cekKontak = $this->kontak->where(['nama' => trim($data[0]), 'tipe' => 'Vendor'])->first();
                    if ($cekKontak) {
                        $idKontak = $cekKontak['id_kontak'];
                    } else {
                        $this->kontak->save([
                            'tipe' => 'Vendor',
                            'nama' => trim($data[0]),
                            'perusahaan' => trim($data[1]),
                            'alamat' => trim($data[2]),
                            'telepon' => '6281',
                            'email' => trim($data[0]) . '@gmail.com',
                        ]);
                        $idKontak = $this->satuan->getInsertID();
                    }
                } else {
                    $idKontak = null;
                }

                if ($expired != '') {
                    $expiredBarang = date('Y-m-d H:i:s', strtotime($expired));
                } else {
                    $expiredBarang = null;
                }

                //Cek margin harga
                $hitungHarga = $hargaJual - $hargaBeli;
                if ($hargaBeli != 0) {
                    $margin = @($hitungHarga / $hargaBeli) * 100;
                } else {
                    $margin = 0;
                }

                $uuid = Uuid::uuid4();
                $suuid = new ShortUUID();
                //Ambil max id_barang dan kode jual toko
                $query = $this->barang->selectMax('id_barang', 'last');
                $hasil = $query->get()->getRowArray();
                $last = $hasil['last'] + 1;
                $noKode = sprintf('%02s', $last);
                $toko = $this->toko->first();
                $kdBarang = $toko['kode_barang'];
                $kodeBarang = $kdBarang . $noKode;

                $simpandata = [
                    'uuid_barang' => $suuid->encode($uuid),
                    'kode_barang' => $kodeBarang,
                    'barcode' => $barcode,
                    'nama_barang' => $namaBarang,
                    'merk' => $merk,
                    'harga_beli' => $hargaBeli,
                    'harga_jual' => $hargaJual,
                    'harga_member' => 0,
                    'diskon' => $diskon,
                    'diskon_persen' => $diskonPersen,
                    'satuan_barang' => $namaSatuan,
                    'satuan_nilai' => $nilaiSatuan,
                    'deskripsi' => $deskripsi,
                    'stok' => $stok,
                    'active' => 1,
                    'id_kategori' => $idKategori,
                    'stok_min' => $stokMin,
                    'id_kontak' => $idKontak,
                    'expired' => $expiredBarang,
                    'sku' => $sku,
                    'id_toko' => 1,
                    'margin' => number_format($margin, 2)
                ];

                // Fungsi cek barang untuk cek nama barang yang sama
                if ($ignoreName == true) {
                    $cekKode = array();
                } else {
                    $cekKode = $this->barang->getWhere(['nama_barang' => $namaBarang])->getResult();
                }

                if (count($cekKode) > 0) {
                    session()->setFlashdata('error', 'Import data gagal karena Nama Barang sudah ada');
                } else {
                    $this->barang->save($simpandata);

                    //Save Log
                    $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Import Barang Excel']);

                    session()->setFlashdata('success', 'Proses Import data Excel Berhasil');
                }
            }

            return redirect()->to('/excel/import');
        }
    }
}
