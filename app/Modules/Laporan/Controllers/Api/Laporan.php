<?php

namespace App\Modules\Laporan\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Laporan\Models\LaporanBarangModel;
use App\Modules\Laporan\Models\LaporanPenjualanModel;
use App\Modules\Laporan\Models\LaporanKategoriModel;
use App\Modules\Laporan\Models\LaporanNotaitemModel;
use App\Modules\Laporan\Models\LaporanCashflowModel;
use App\Modules\Laporan\Models\LaporanLogModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends BaseControllerApi
{
    protected $format       = 'json';
    protected $barang;
    protected $penjualan;
    protected $kategori;
    protected $jualItem;
    protected $cash;
    protected $log;

    public function __construct()
    {
       $this->barang = new LaporanBarangModel();
       $this->penjualan = new LaporanPenjualanModel();
       $this->kategori = new LaporanKategoriModel();
       $this->jualItem = new LaporanNotaitemModel();
       $this->cash = new LaporanCashflowModel();
       $this->log = new LaporanLogModel();
    }

    public function cashflow()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->cash->getLaporanByCashflow($start, $end);
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

    public function penjualan()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->penjualan->getLaporanByPenjualan($start, $end);
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

    public function barang()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->barang->getLaporanByBarang($start, $end);
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

    public function stok()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->barang->getLaporanByStok($start, $end);
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

    public function kategori()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->kategori->getLaporanByKategori($start, $end);
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

    public function detailKategori()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $id = $input['id_kategori'];
        return $this->respond(["status" => true, "message" => lang('App.getSuccess'), "data" => $this->jualItem->detailLaporanByKategori($start, $end, $id)], 200);
    }

    public function LabaRugi()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data['sumPenjualan'] = $this->cash->sumPenjualan($start, $end);
        $data['sumPemasukanLain'] = $this->cash->sumPemasukanLain($start, $end);
        $totalPendapatan = $data['sumPenjualan'] + $data['sumPemasukanLain'];
        $data['sumHPP'] = $this->penjualan->sumHPP($start, $end);
        $labaKotor = $totalPendapatan - $data['sumHPP'];
        $data['sumPengeluaran'] = $this->cash->sumPengeluaran($start, $end);
        $data['sumPengeluaranLain'] = $this->cash->sumMutasiBank($start, $end);
        $totalPengeluaran = $data['sumPengeluaran'] +  $data['sumPengeluaranLain'];
        $labaBersih = $labaKotor - $totalPengeluaran;
        foreach ($data as $key => $value) {
            $arrayData = [
                'pemasukan_penjualan' => $data['sumPenjualan'],
                'pemasukan_lain' => $data['sumPemasukanLain'],
                'total_pendapatan' => $totalPendapatan,
                'beban_pokok_pendapatan' => $data['sumHPP'],
                'laba_kotor' => $labaKotor,
                'pengeluaran' => $data['sumPengeluaran'],
                'pengeluaran_lain' => $data['sumPengeluaranLain'],
                'total_pengeluaran' => $totalPengeluaran,
                'laba_bersih' => $labaBersih,
            ];
        }

        if (!empty($arrayData)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $arrayData
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

    public function excelExport()
    {
        $input = $this->request->getVar('data');
        $data = json_decode($input, true);

        $spreadsheet = new Spreadsheet();

        // tulis header/nama kolom 
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'ID Barang')
            ->setCellValue('C1', 'Barcode')
            ->setCellValue('D1', 'Nama Barang')
            ->setCellValue('E1', 'Merk')
            ->setCellValue('F1', 'Harga Beli')
            ->setCellValue('G1', 'Harga Jual')
            ->setCellValue('H1', 'Satuan')
            ->setCellValue('I1', 'Deskripsi')
            ->setCellValue('J1', 'Stok')
            ->setCellValue('K1', 'Aktif')
            ->setCellValue('L1', 'Tgl Input')
            ->setCellValue('M1', 'Tgl Update');
        $column = 2;
        // tulis data ke cell
        $no = 1;
        foreach ($data as $data) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column, $data['id_barang'])
                ->setCellValue('C' . $column, $data['barcode'])
                ->setCellValue('D' . $column, $data['nama_barang'])
                ->setCellValue('E' . $column, $data['merk'])
                ->setCellValue('F' . $column, $data['harga_beli'])
                ->setCellValue('G' . $column, $data['harga_jual'])
                ->setCellValue('H' . $column, $data['satuan_barang'])
                ->setCellValue('I' . $column, $data['deskripsi'])
                ->setCellValue('J' . $column, $data['stok'])
                ->setCellValue('K' . $column, $data['active'])
                ->setCellValue('L' . $column, $data['created_at'])
                ->setCellValue('M' . $column, $data['updated_at']);
            $column++;
        }
        // tulis dalam format .xlsx
        $writer = new Xlsx($spreadsheet);
        $fileName = 'ImportData-' . getdate()[0] . '.xlsx';
        $writer->save('files/export/' . $fileName);
        $fileXlsx = base_url('files/export/' . $fileName);

        $response = [
            'status' => true,
            'message' => lang('App.getSuccess'),
            'data' => ['filename' => $fileName, 'url' => $fileXlsx],
        ];
        return $this->respond($response, 200);
    }

    public function stokOpname()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = $this->stokopname->getStokOpname($start, $end);
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


    public function log()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->log->getLaporanLog($start, $end);
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
}
