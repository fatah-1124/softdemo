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
            font-size: 12px;
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
        <h4>Penjualan</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- <th scope="col">#</th> -->
                    <th scope="col">Faktur</th>
                    <th scope="col">Tgl/Jam</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Grup</th>
                    <th scope="col">Item</th>
                    <th scope="col">DPP</th>
                    <th scope="col">Diskon</th>
                    <th scope="col">Lainnya</th>
                    <th scope="col">Jumlah*</th>
                    <th scope="col">Laba</th>
                    <th scope="col">Bayar</th>
                    <th scope="col">Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data as $row) : ?>
                    <tr>
                        <!-- <td><?= $no++; ?></td> -->
                        <td><?= $row['faktur']; ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                        <td><?= $row['nama_kontak']; ?></td>
                        <td><?= str_replace("_", " ", ucfirst($row['grup'])); ?></td>
                        <td><?= $row['jumlah']; ?></td>
                        <td><?= Ribuan($row['subtotal']); ?></td>
                        <td><?= Ribuan($row['diskon']); ?></td>
                        <td>+<?= Ribuan($row['pajak']); ?> <small>(pajak)</small><br />+<?= Ribuan($row['pembulatan']); ?> <small>(pembulatan)</small><br />=<?= Ribuan((int)$row['pajak']+(int)$row['pembulatan']); ?></td>
                        <td><strong><?= Ribuan($row['total']); ?></strong></td>
                        <td><?= Ribuan($row['total_laba']); ?></td>
                        <td style="font-size: 12px;">
                            <?= strtoupper($row['metode_bayar']); ?><br />
                            <?php if ($row['id_piutang'] == null) { ?>
                                <span style="color: green;">Paid</span>
                            <?php } else { ?>
                                <?php if ($row['status_piutang'] == 1) { ?><span style="color: green;">Paid</span><?php } else { ?><span style="color: red;">Unpaid</span><?php } ?><br />
                                Bayar: <?= Ribuan($row['bayar']); ?><br />
                                Sisa: <?= Ribuan($row['sisa_piutang']); ?><br />
                                Ket.: <?= $row['keterangan'] ?? "-"; ?>
                            <?php } ?>
                        </td>
                        <td><?= $row['nama']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php
                $jumlah = 0;
                $subtotal = 0;
                $pajak = 0;
                $pembulatan = 0;
                $total = 0;
                $totalLaba = 0;
                $sisaPiutang = 0;

                foreach ($data as $row) {
                    $jumlah += $row['jumlah'];
                    $subtotal += $row['subtotal'];
                    $pajak += $row['pajak'];
                    $pembulatan += $row['pembulatan'];
                    $total += $row['total'];
                    $totalLaba += $row['total_laba'];
                    $sisaPiutang += $row['sisa_piutang'];
                }
                ?>
                <tr>
                    <!-- <td></td> -->
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td><?= $jumlah; ?></td>
                    <td><?= Ribuan($subtotal); ?></td>
                    <td></td>
                    <td></td>
                    <td><?= Ribuan($total); ?></td>
                    <td><?= Ribuan($totalLaba); ?></td>
                    <td>- <?= Ribuan($sisaPiutang); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <!-- <td></td> -->
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="3" style="text-align: right;">*Saldo Kas =<br />(Total-Piutang-Pajak)</td>
                    <td><strong><?= Ribuan($total-$sisaPiutang-$pajak); ?></strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>