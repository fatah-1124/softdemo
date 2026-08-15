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
            font-size: 11px;
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
        <h4>Barang Terjual</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Code Item</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col"><?= lang('App.tax'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= date('d-m-Y', strtotime($row['created_at'])); ?></td>
                        <td><?= $row['kode_barang']; ?></td>
                        <td width="150"><?= $row['nama_barang']; ?></td>
                        <td><?= $row['satuan']; ?></td>
                        <td><?= $row['qty']; ?></td>
                        <td><?= Ribuan($row['harga_jual']); ?></td>
                        <td><?= Ribuan($row['jumlah']); ?></td>
                        <td><?= Ribuan($row['ppn']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php
                $totalQty = 0;
                $totalJumlah = 0;
                $totalPajak = 0;
                foreach ($data as $row) {
                    $totalQty += $row['qty'];
                    $totalJumlah += $row['jumlah'];
                    $totalPajak += $row['ppn'];
                }
                ?>
                <tr>
                    <td colspan="4"></td>
                    <td align="right">Total</td>
                    <td><?= $totalQty; ?></td>
                    <td></td>
                    <td><?= Ribuan($totalJumlah); ?></td>
                    <td><?= Ribuan($totalPajak); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>