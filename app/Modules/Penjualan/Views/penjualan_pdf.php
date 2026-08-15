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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Print PDF</title>
    <style>
        @media print {
            @page {
                margin: 0 auto;
                /* imprtant to logo margin */
                sheet-size: 200px 150mm;
                /* imprtant to set paper size */
            }

            html,
            body {
                margin: 0;
                padding: 0;
            }

            #printContainer {
                width: 80px;
                margin: left;
                /*padding: 10px;*/
                /*border: 2px dotted #000;*/
                text-align: justify;
                font-size: 85%;
            }
        }
    </style>
</head>

<body>
    <div id="printContainer">
        <div style="line-height: normal;">
            <div style="text-align:center;margin-bottom: 10px;">
                <img src="<?= base_url() . $logo; ?>" width="50" height="50" alt="Logo">
            </div>
            <div style="text-align:center;margin-bottom: 10px;"><strong><?= $toko['nama_toko']; ?></strong></div>
            <?php if($toko['NIB'] != 0) : ?><div style="text-align:center;font-size: 10px;">NIB: <?= $toko['NIB']; ?></div><?php endif; ?>
            <div style="text-align:center;font-size: 10px;"><?= $toko['alamat_toko']; ?></div>
            <div style="text-align:center;font-size: 10px;">Telp/WA: <?= $toko['telp']; ?></div>
            <hr />
            <div style="text-align:left;font-size: 9px;">
                No: <?= $penjualan['faktur']; ?><br />
                Hr/Tgl: <?= dayshortdate_indo(date('Y-m-d', strtotime($penjualan['created_at']))) . ' ' . date('H:i', strtotime($penjualan['created_at'])); ?><br />
                Kasir: <?= $user ?><br />
                Customer: <?= $penjualan['nama_kontak'] ?>
            </div>
            <hr />
            <?php foreach ($item as $item) { ?>
                <span style="font-size: 10px;"><?= $item->nama_barang; ?></span><br />
                <div style="font-size: 10px;text-align: right;">
                    <?= $item->qty ?> <?= $item->satuan ?> x <?= Ribuan($item->harga_jual) ?>
                    .....
                    <span><?= Ribuan($item->jumlah) ?></span>
                </div>
            <?php } ?>
            <hr />
            <div style="text-align: right;">
                Subtotal (<?= $penjualan['jumlah']; ?> item): <?= Ribuan($penjualan['subtotal']) ?><br />
                PPN <?= $penjualan['PPN'] ?>%: <?= Ribuan($penjualan['pajak']) ?><br />
                Diskon <?= $penjualan['diskon_persen'] ?>%: <?= Ribuan($penjualan['diskon']) ?><br />
                Total: <?= Ribuan($penjualan['total']) ?><br />
                Bayar: <?= Ribuan($penjualan['bayar']) ?><br />
                <?php if ($penjualan['kembali'] >= 0) { ?>
                    Kembali: <?= Ribuan($penjualan['kembali']) ?><br />
                <?php } else { ?> 
                    Kurang: <?= Ribuan($penjualan['kembali']) ?><br />
                <?php } ?>
            </div>
            <br/>
            <div style="text-align:center;font-size: 8px;">
            <?= $toko['footer_nota']; ?>. Dicetak menggunakan Aplikasi <?= $appname ?> by <?= $companyname ?>
            </div>
        </div>
    </div>

</body>

</html>