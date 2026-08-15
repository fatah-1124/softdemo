<?php
function Ribuan($angka)
{
    $hasil_rupiah = number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Penjualan <?= $faktur; ?></title>
    <style>
        html,
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        @media print {
            @page {
                margin-left: 0px;
                margin-right: 0px;
                margin-top: 0px;
                margin-bottom: 0px;
            }
            html,
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, Helvetica, sans-serif;
            }
            #printContainer {
                margin: left;
                padding: 10px;
                text-align: justify;
                font-size: 100%;
            }
        }
    </style>
</head>

<body onLoad="javascript:window.print();">
    <div id="printContainer">
        <div style="line-height: normal;">
            <div style="text-align:center;margin-bottom: 10px;margin-top: 20px;">
                <img src="<?= base_url() . $logo; ?>" width="50" height="50" alt="Logo">
            </div>
            <div style="text-align:center;margin-bottom: 10px;"><strong><?= $toko['nama_toko']; ?></strong></div>
            <?php if ($toko['NIB'] != 0) : ?><div style="text-align:center;font-size: 12px;">NIB: <?= $toko['NIB']; ?></div><?php endif; ?>
            <div style="text-align:center;font-size: 12px;"><?= $toko['alamat_toko']; ?></div>
            <div style="text-align:center;font-size: 12px;">Telp/WA: <?= $toko['telp']; ?></div>
            <hr />
            <div style="text-align:left;font-size: 12px;">
                No: <?= $penjualan['faktur']; ?><br />
                Hr/Tgl: <?= dayshortdate_indo(date('Y-m-d', strtotime($penjualan['created_at']))) . ' ' . date('H:i', strtotime($penjualan['created_at'])); ?><br />
                Kasir: <?= $user ?><br />
                Customer: <?= $penjualan['nama_kontak'] ?> (<?= $penjualan['grup'] ?>)
            </div>
            <hr />
            <?php foreach ($item as $item) { ?>
                <p style="font-size: 12px;">
                    <?= $item->nama_barang; ?> <br />
                    <?= $item->qty ?> (<?= $item->satuan ?>) x <?= Ribuan($item->harga_jual_grosir == 0 ? $item->harga_jual : ($item->qty >= $item->jumlah_min_grosir ? $item->harga_jual_grosir : $item->harga_jual)) ?>
                    <span style="float: right;">Rp<?= Ribuan($item->jumlah) ?></span>
                </p>
            <?php } ?>
            <hr />
            <div style="text-align: right;font-size: 12px;">
                DPP (<?= $penjualan['jumlah']; ?> item): <?= Ribuan($penjualan['subtotal']) ?><br />
                PPN <?= $penjualan['PPN'] ?>%: <?= Ribuan($penjualan['pajak']) ?><br />
                Diskon <?= $penjualan['diskon_persen'] ?>%: <?= Ribuan($penjualan['diskon']) ?><br />
                <?php if ($toko['pembulatan'] == 1) : ?>
                    Pembulatan: <?= Ribuan($penjualan['pembulatan']) ?><br />
                <?php endif ?>
                <strong>Total: <?= Ribuan($penjualan['total']) ?></strong><br />
                Bayar: <?= Ribuan($penjualan['bayar']) ?><br />
                <?php if ($penjualan['kembali'] >= 0) { ?>
                    Kembali: <?= Ribuan($penjualan['kembali']) ?><br />
                <?php } else { ?>
                    Kurang: <?= Ribuan($penjualan['kembali']) ?><br />
                <?php } ?>
            </div>
            <br />
            <div style="text-align:center;font-size: 10px;">
                <?= $toko['footer_nota']; ?>. Dicetak menggunakan Aplikasi <?= $appname ?> by <?= $companyname ?>
            </div>
        </div>
    </div>
</body>

</html>