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
    <title>Print Barcode</title>
    <style>
        @media print {

            html,
            body {
                height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }
        }

        .box {
            position: relative;
            width: 188px !important;
            height: 94px !important;
            border: 1px solid #999;
            text-align: center;
            top: 0%;
            left: 50%;
            /* bring your own prefixes */
            transform: translate(-50%, -0%);
        }

        .barang {
            font-size: 14px;
            text-transform: uppercase !important;
            line-height: 1.5;
        }

        .rotated {
            font-size: 10px;
            writing-mode: tb-rl;
            transform: rotate(-180deg);
            float: left;
        }

        .harga {
            font-size: 14px;
            text-transform: uppercase !important;
        }
    </style>
</head>

<body onLoad="javascript:window.print();">
    <?php if ($jumlah == 1) { ?>
        <div class="box">
            <span class="rotated">** <?= $toko['nama_toko']; ?> **</span>
            <div class="barang"><?= character_limiter($barang['nama_barang'], 15, '...'); ?></div>
            <?php echo '<img src="data:image/png;base64,' . $barcode->getBarcodePNG($text, $tipe, 1, 50, array(1, 1, 1), true) . '" alt="barcode" />'; ?>
            <div class="harga">Rp<?= Ribuan($barang['harga_jual']); ?> / <?= $barang['satuan_barang']; ?></div>
        </div>
    <?php } else { ?>
        <?php
        for ($i = 1; $i <= $jumlah; $i++) { ?>
            <div class="box" style="margin-bottom: 25px;">
                <span class="rotated">** <?= $toko['nama_toko']; ?> **</span>
                <div class="barang"><?= character_limiter($barang['nama_barang'], 15, '...'); ?></div>
                <?php echo '<img src="data:image/png;base64,' . $barcode->getBarcodePNG($text, $tipe, 1, 50, array(1, 1, 1), true) . '" alt="barcode" />'; ?>
                <div class="harga">Rp<?= Ribuan($barang['harga_jual']); ?> / <?= $barang['satuan_barang']; ?></div>
            </div>
        <?php }
        ?>
    <?php } ?>

</body>

</html>