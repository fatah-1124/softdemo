<?php
function Ribuan($angka)
{

    $hasil_rupiah = number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <title>Print PDF</title>
    <style>
        .container {
            padding-left: 10px;
        }

        table {
            border: 1px solid #424242;
            border-collapse: collapse;
            padding: 0;
        }

        th {
            background-color: #f2f2f2;
            color: black;
            padding: 15px;
        }

        tr,
        td {
            border-bottom: 1px solid #ddd;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- <img src="<?= base_url() . '/' . $logo; ?>" width="80" height="80" alt="Logo" style="float:left;margin-top: 10px;margin-right: 10px;"> -->
        <h1><?= $toko['nama_toko']; ?></h1>
        <?= $toko['alamat_toko']; ?> - Telp/WA: <?= $toko['telp']; ?> - Email: <?= $toko['email']; ?> - NIB: <?= $toko['NIB']; ?>
        <hr />
        <h1 align="center">Laporan <?= mediumdate_indo($tgl_start); ?> &mdash; <?= mediumdate_indo($tgl_end); ?></h1>
        <h4>Laba Rugi (<?= date('d-m-Y', strtotime($tgl_start)); ?> - <?= date('d-m-Y', strtotime($tgl_end)); ?>)</h4>
        <table class="table table-bordered" style="width: 100% !important;">
            <tbody>
                <tr>
                    <th colspan="4"><strong>Pendapatan</strong></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td>Penjualan</td>
                    <td colspan="4"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-left: 40px;">Pemasukan Penjualan (KAS)</td>
                    <td><?= Ribuan($data['pemasukan_penjualan']) ?? "0"; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-left: 40px;">Penghasilan Lain (Pemasukan Lainnya &amp; Mutasi)</td>
                    <td><?= Ribuan($data['pemasukan_lain']) ?? "0"; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Total Pendapatan</strong></td>
                    <td colspan="4"></td>
                    <td><?= Ribuan($data['total_pendapatan']) ?? "0"; ?></td>
                </tr>

                <tr>
                    <th><strong>Beban Pokok Penjualan</strong></th>
                    <th colspan="4"></th>
                    <th></th>
                </tr>
                <tr>
                    <td colspan="4" style="padding-left: 40px;">Beban Pokok Pendapatan (HPP)</td>
                    <td><?= Ribuan($data['beban_pokok_pendapatan']) ?? "0"; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Total Beban Pokok Penjualan</strong></td>
                    <td colspan="4"></td>
                    <td><?= Ribuan($data['beban_pokok_pendapatan']) ?? "0"; ?></td>
                </tr>

                <tr>
                    <th><strong>Laba Kotor</strong></th>
                    <th colspan="4"></th>
                    <th><?= Ribuan($data['laba_kotor']) ?? "0"; ?></th>
                </tr>

                <br/>

                <tr>
                    <th><strong>Biaya Operasional</strong></th>
                    <th colspan="4"></th>
                    <th></th>
                </tr>
                <tr>
                    <td colspan="4" style="padding-left: 40px;">Pengeluaran & Biaya (KAS)<br /><small>(Termasuk Potongan Pembulatan Kebawah)</small></td>
                    <td><?= Ribuan($data['pengeluaran']) ?? "0"; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-left: 40px;">Pengeluaran Lain</td>
                    <td><?= Ribuan($data['pengeluaran_lain']) ?? "0"; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Total Biaya Operasional</strong></td>
                    <td colspan="4"></td>
                    <td><?= Ribuan($data['total_pengeluaran']) ?? "0"; ?></td>
                </tr>

                <tr>
                    <th><strong>Laba Bersih</strong></th>
                    <th colspan="4"></th>
                    <th><?= Ribuan($data['laba_bersih']) ?? "0"; ?></th>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>