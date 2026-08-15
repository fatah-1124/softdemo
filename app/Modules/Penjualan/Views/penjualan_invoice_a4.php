<?php
function Ribuan($angka)
{
    $hasil_rupiah = number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}

function terbilang($x)
{
    $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

    if ($x < 12)
        return " " . $angka[$x];
    elseif ($x < 20)
        return terbilang($x - 10) . " belas";
    elseif ($x < 100)
        return terbilang($x / 10) . " puluh" . terbilang($x % 10);
    elseif ($x < 200)
        return "seratus" . terbilang($x - 100);
    elseif ($x < 1000)
        return terbilang($x / 100) . " ratus" . terbilang($x % 100);
    elseif ($x < 2000)
        return "seribu" . terbilang($x - 1000);
    elseif ($x < 1000000)
        return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
    elseif ($x < 1000000000)
        return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <title>Invoice Penjualan <?= $faktur; ?></title>
    <style>
        @page {
            size: A4,
        }

        html,
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .sheet {
            overflow: visible;
            height: auto !important;
        }

        @media print {
            @page: first {
                margin-left: 0px;
                margin-right: 0px;
                margin-top: 0px;
                margin-bottom: 50px;
            }

            @page {
                margin-left: 0px;
                margin-right: 0px;
                margin-top: 50px;
                margin-bottom: 0px;
            }

            html,
            body {
                margin: 0;
                padding: 10px;
                font-family: Arial, Helvetica, sans-serif;

            }

            #printContainer {
                margin: left;
                padding: 10px;
                text-align: justify;
                font-size: 100%;
            }

            .sheet {
                overflow: visible;
                height: auto !important;
            }
        }
    </style>
</head>

<body class="A4" onLoad="javascript:window.print();">
    <section class="sheet padding-10mm">
        <div id="printContainer">
            <div style="line-height: normal;">
                <div class="row">
                    <div class="col-5">
                        <img src="<?= base_url() . $logo; ?>" width="30" height="30" alt="Logo" style="float: left;margin-right: 10px;">
                        <h4 style="text-align:left;margin-bottom: 10px;"><strong><?= $toko['nama_toko']; ?></strong></h4>
                        <?php if ($toko['NIB'] != 0) : ?><div style="text-align:left;font-size: 14px;">NIB: <?= $toko['NIB']; ?></div><?php endif; ?>
                        <div style="text-align:left;font-size: 14px;"><?= $toko['alamat_toko']; ?></div>
                        <div style="text-align:left;font-size: 14px;">Telp: <?= $toko['telp']; ?></div>
                        <div style="text-align:left;font-size: 14px;">Email: <?= $toko['email']; ?></div>
                    </div>
                    <div class="col-7">
                        <div class="float-end">
                            <h3><strong>INVOICE</strong> <?= $faktur; ?></h3>
                            <div class="card float-end" style="width: 14rem;">
                                <div class="card-header">
                                    DATE
                                </div>
                                <div class="card-body">
                                    <?= dayshortdate_indo(date('Y-m-d', strtotime($penjualan['created_at']))) . ' ' . date('H:i', strtotime($penjualan['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div style="text-align:left;font-size: 16px;">
                    <h6><strong>Customer:</strong> <?= $penjualan['nama_kontak'] ?> <?php if ($penjualan['grup'] != ''): ?>(<?= $penjualan['grup']?>)<?php endif; ?> <?php if ($penjualan['perusahaan'] != ''): ?>(<?= $penjualan['perusahaan']; ?>)<?php endif; ?></h6>
                    Telp/Email: <?= $penjualan['telepon']; ?> / <?= $penjualan['email']; ?><br />
                    Alamat: <?= $penjualan['alamat'] ?><br />
                </div>
                <hr />
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">NO</th>
                            <th scope="col">NAME</th>
                            <th scope="col">CODE</th>
                            <th scope="col">QTY</th>
                            <th scope="col">UNIT</th>
                            <th scope="col">PRICE (RP)</th>
                            <th scope="col">TOTAL (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($item as $item) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td width="400"><?= $item->nama_barang; ?></td>
                                <td><?= $item->kode_barang; ?></td>
                                <td><?= $item->qty ?></td>
                                <td><?= $item->satuan ?></td>
                                <td><?= Ribuan($item->harga_jual_grosir == 0 ? $item->harga_jual : ($item->qty >= $item->jumlah_min_grosir ? $item->harga_jual_grosir : $item->harga_jual)) ?></td>
                                <td><?= Ribuan($item->jumlah) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-8">
                        <p style="line-height: 1.5">Terbilang : <em><?= terbilang($penjualan['total']); ?> rupiah</em></p>
                        <p style="line-height: 1.5">Catatan : <?= $penjualan['catatan']; ?></p>
                        <table class="table table-bordered table-sm" style="width: 250px;">
                            <tbody>
                                <tr>
                                    <th scope="row">Payment</th>
                                    <td align="center">:</td>
                                    <td><?= ucfirst($penjualan['metode_bayar']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Bayar Rp</th>
                                    <td align="center">:</td>
                                    <td width="50%"><?= Ribuan($penjualan['bayar']) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php if ($penjualan['kembali'] >= 0) { ?>
                                            Kembali
                                        <?php } else { ?>
                                            Kurang
                                        <?php } ?>
                                    </th>
                                    <td align="center">:</td>
                                    <td><?= Ribuan($penjualan['kembali']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-4">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>DPP (<?= $penjualan['jumlah']; ?> item)</td>
                                    <td align="center">:</td>
                                    <td><?= Ribuan($penjualan['subtotal']) ?></td>
                                </tr>
                                <tr>
                                    <td>PPN <?= $penjualan['PPN'] ?>%</td>
                                    <td align="center">:</td>
                                    <td width="50%"><?= Ribuan($penjualan['pajak']) ?></td>
                                </tr>
                                <tr>
                                    <td>Diskon <?= $penjualan['diskon_persen'] ?>%</td>
                                    <td align="center">:</td>
                                    <td><?= Ribuan($penjualan['diskon']) ?></td>
                                </tr>
                                <?php if ($toko['pembulatan'] == 1) : ?>
                                    <tr>
                                        <td>Pembulatan</td>
                                        <td align="center">:</td>
                                        <td><?= Ribuan($penjualan['pembulatan']) ?></td>
                                    </tr>
                                <?php endif ?>
                                <tr>
                                    <td>Total Rp</td>
                                    <td align="center">:</td>
                                    <td><?= Ribuan($penjualan['total']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br />

                <table style="text-align: center;font-size: 16px;">
                    <tbody>
                        <tr>
                            <td width="600">
                                <?= $toko['footer_invoice_ttd1']; ?>
                            </td>

                            <td width="600">
                                <?= $toko['footer_invoice_ttd2']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <br />
                <br />
                <div style="text-align:left;font-size: 14px;">
                    <p><em><strong>PERHATIAN</strong></em></p>
                    <?= $toko['footer_nota']; ?>
                </div>
                <br />
                <br />
                <div style="text-align:center;font-size: 14px;">
                    <em>Print by: <?= $user ?>, <?= date('d-m-Y H:i:s'); ?>.<br />Dicetak menggunakan Aplikasi <?= $appname ?> by <?= $companyname ?></em>
                </div>
            </div>
        </div>
    </section>
</body>

</html>