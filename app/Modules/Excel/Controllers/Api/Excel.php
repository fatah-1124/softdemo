<?php

namespace App\Modules\Excel\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Barang\Models\BarangModel;
use App\Modules\Log\Models\LogModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = BarangModel::class;
    protected $log;

    public function __construct()
    {
        $this->log = new LogModel();
    }

    public function excelExport()
    {
        $input = $this->request->getVar('data');
        $data = json_decode($input, true);

        $spreadsheet = new Spreadsheet();

        // tulis header/nama kolom 
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'ID')
            ->setCellValue('C1', 'Barcode')
            ->setCellValue('D1', 'Nama Barang')
            ->setCellValue('E1', 'Merk')
            ->setCellValue('F1', 'Harga Beli')
            ->setCellValue('G1', 'Harga Jual')
            ->setCellValue('H1', 'Harga Member')
            ->setCellValue('I1', 'Satuan')
            ->setCellValue('J1', 'Deskripsi')
            ->setCellValue('K1', 'Stok')
            ->setCellValue('L1', 'Stok Min')
            ->setCellValue('M1', 'Stok Gudang')
            ->setCellValue('N1', 'Aktif')
            ->setCellValue('O1', 'Vendor/Supplier')
            ->setCellValue('P1', 'Expired')
            ->setCellValue('Q1', 'SKU')
            ->setCellValue('R1', 'UUID')
            ->setCellValue('S1', 'Tgl Input')
            ->setCellValue('T1', 'Tgl Update');
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
                ->setCellValue('H' . $column, $data['harga_member'])
                ->setCellValue('I' . $column, $data['satuan_barang'])
                ->setCellValue('J' . $column, $data['deskripsi'])
                ->setCellValue('K' . $column, $data['stok'])
                ->setCellValue('L' . $column, $data['stok_min'])
                ->setCellValue('M' . $column, $data['stok_gudang'])
                ->setCellValue('N' . $column, $data['active'])
                ->setCellValue('O' . $column, $data['vendor_supplier'] . ' ' . $data['perusahaan'])
                ->setCellValue('P' . $column, $data['expired'])
                ->setCellValue('Q' . $column, $data['sku'])
                ->setCellValue('R' . $column, $data['uuid_barang'])
                ->setCellValue('S' . $column, $data['created_at'])
                ->setCellValue('T' . $column, $data['updated_at']);
            $column++;
        }
        // tulis dalam format .xlsx
        $writer = new Xlsx($spreadsheet);
        $fileName = 'ExportData-' . getdate()[0] . '.xlsx';
        $writer->save('files/export/' . $fileName);
        $fileXlsx = base_url('files/export/' . $fileName);

        //Save Log
        $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Export Barang Excel']);

        $response = [
            'status' => true,
            'message' => lang('App.getSuccess'),
            'data' => ['filename' => $fileName, 'url' => $fileXlsx],
        ];
        return $this->respond($response, 200);
    }

    
}
