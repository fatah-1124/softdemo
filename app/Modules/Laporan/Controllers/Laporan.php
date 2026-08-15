<?php

namespace  App\Modules\Laporan\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Libraries\Settings;
use App\Modules\Laporan\Models\LaporanBarangModel;
use App\Modules\Laporan\Models\LaporanPenjualanModel;
use App\Modules\Laporan\Models\LaporanKategoriModel;
use App\Modules\Laporan\Models\LaporanCashflowModel;
use App\Modules\Toko\Models\TokoModel;
use TCPDF;
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\I18n\Time;

class Laporan extends BaseController
{
    protected $permission;
    protected $setting;
    protected $barang;
    protected $penjualan;
    protected $kategori;
    protected $cash;
    protected $toko;

    public function __construct()
    {
        //memanggil Model
        $this->permission = new Permission();
        $this->setting = new Settings();
        $this->barang = new LaporanBarangModel();
        $this->penjualan = new LaporanPenjualanModel();
        $this->kategori = new LaporanKategoriModel();
        $this->cash = new LaporanCashflowModel();
        $this->toko = new TokoModel();
        helper('tglindo');
    }


    public function index()
    {
        return view('App\Modules\Laporan\Views/laporan', [
            'title' => lang('App.report'),
            'permissions' => $this->permission->init(),
            'startDate' => date('Y-m-d', strtotime('-3 month', strtotime(Time::now()))),
            'endDate' => date('Y-m-d', strtotime(Time::now())),
            'hariini' => date('Y-m-d', strtotime(Time::now())),
            'kemarin' => date('Y-m-d', strtotime('-1 day', strtotime(Time::now()))),
            'tujuhHari' => date('Y-m-d', strtotime('-1 week', strtotime(Time::now()))),
            'awalBulan' => date('Y-m-', strtotime(Time::now())) . '01',
            'akhirBulan' => date('Y-m-t', strtotime(Time::now())),
            'awalTahun' => date('Y-', strtotime(Time::now())) . '01-01',
            'akhirTahun' => date('Y-', strtotime(Time::now())) . '12-31',
            'awalTahunLalu' => date('Y-', strtotime('-1 year', strtotime(Time::now()))) . '01-01',
            'akhirTahunLalu' => date('Y-', strtotime('-1 year', strtotime(Time::now()))) . '12-31',
            'satuBulanAwal' => date('Y-m-d', strtotime('-1 month', strtotime(Time::now()))),
            'satuBulanAkhir' => date('Y-m-d', strtotime('-1 day', strtotime(Time::now()))),
            'tigaBulanAwal' => date('Y-m-d', strtotime('-3 month', strtotime(Time::now()))),
            'tigaBulanAkhir' => date('Y-m-d', strtotime(Time::now())),
        ]);
    }

    public function cashflowPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->cash->getLaporanByCashflow($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/cash_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('L', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/penjualan.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/penjualan.pdf');
        $pdf->Output('cash.pdf', 'I');  // display on the browser
    }

    public function bankPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->bank->getLaporanByBank($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/bank_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('L', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/penjualan.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/penjualan.pdf');
        $pdf->Output('bank.pdf', 'I');  // display on the browser
    }

    public function penjualanPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->penjualan->getLaporanByPenjualan($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/penjualan_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('L', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/penjualan.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/penjualan.pdf');
        $pdf->Output('penjualan.pdf', 'I');  // display on the browser
    }

    public function penjualanExcel()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];

        $data = $this->penjualan->getLaporanByPenjualan($start, $end);
        $toko = $this->toko->first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', $toko['nama_toko'] ?? 'Laporan Penjualan');
        $sheet->setCellValue('A2', 'Periode: ' . $start . ' s/d ' . $end);
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . date('d-m-Y H:i'));

        // Header
        $headers = [
            'Faktur',
            'Tgl/Jam',
            'Customer',
            'Grup',
            'Item',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Pembulatan',
            'Total',
            'Laba',
            'Bayar',
            'Sisa',
            'Metode Bayar',
            'Keterangan',
            'Kasir'
        ];

        $sheet->fromArray($headers, null, 'A5');

        // Data
        $row = 6;
        $jumlah = $subtotal = $pajak = $pembulatan = $total = $totalLaba = $sisaPiutang = 0;

        foreach ($data as $d) {
            $sheet->fromArray([
                $d['faktur'],
                date('d-m-Y H:i', strtotime($d['created_at'])),
                $d['nama_kontak'],
                str_replace("_", " ", ucfirst($d['grup'])),
                $d['jumlah'],
                $d['subtotal'],
                $d['diskon'],
                $d['pajak'],
                $d['pembulatan'],
                $d['total'],
                $d['total_laba'],
                $d['bayar'],
                $d['sisa_piutang'],
                $d['metode_bayar'] . ($d['id_piutang'] == null ? ' (Paid)' : ($d['status_piutang'] == 1 ? ' (Paid)' : ' (Unpaid)')),
                $d['catatan'] ?? '-',
                $d['nama']
            ], null, 'A' . $row);

            $jumlah += $d['jumlah'];
            $subtotal += $d['subtotal'];
            $pajak += $d['pajak'];
            $pembulatan += $d['pembulatan'];
            $total += $d['total'];
            $totalLaba += $d['total_laba'];
            $sisaPiutang += $d['sisa_piutang'];

            $row++;
        }

        // Total baris
        $sheet->setCellValue("D{$row}", 'Total');
        $sheet->setCellValue("E{$row}", $jumlah);
        $sheet->setCellValue("F{$row}", $subtotal);
        $sheet->setCellValue("J{$row}", $total);
        $sheet->setCellValue("K{$row}", $totalLaba);
        $sheet->setCellValue("M{$row}", $sisaPiutang);

        $row++;

        // Saldo Kas
        $sheet->setCellValue("I{$row}", 'Saldo Kas = Total - Piutang - Pajak');
        $sheet->setCellValue("J{$row}", $total - $sisaPiutang - $pajak);

        // Format kolom angka
        $sheet->getStyle("D6:N{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Auto width
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Penjualan_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function barangPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->barang->getLaporanByBarang($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/barang_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('P', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/barang.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/barang.pdf');
        $pdf->Output('barang.pdf', 'I');  // display on the browser
    }

    public function stokbarangPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->barang->getLaporanByStok($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/stokbarang_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('P', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/stokbarang.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/stokbarang.pdf');
        $pdf->Output('stokbarang.pdf', 'I');  // display on the browser
    }

    public function kategoriPdf()
    {
        $input = $this->request->getVar();
        $start = $input['tgl_start'];
        $end = $input['tgl_end'];
        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $this->kategori->getLaporanByKategori($start, $end)
        ];

        $html = view('App\Modules\Laporan\Views/kategori_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('P', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/kategori.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/kategori.pdf');
        $pdf->Output('kategori.pdf', 'I');  // display on the browser
    }

    public function labarugiPdf()
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

        $data = [
            'toko' => $this->toko->first(),
            'logo' => $this->setting->info['img_logo_resize'],
            'tgl_start' => $start,
            'tgl_end' => $end,
            'data' => $arrayData
        ];

        $html = view('App\Modules\Laporan\Views/labarugi_pdf', $data);

        // create new PDF document
        $pdf = new Html2Pdf('P', 'A4');

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html);
        $this->response->setContentType('application/pdf');
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        //$file = FCPATH.'files/labarugi.pdf';
        //$pdf->Output($file, 'F');
        //$attachment = base_url('files/labarugi.pdf');
        $pdf->Output('labarugi.pdf', 'I');  // display on the browser
    }
}
