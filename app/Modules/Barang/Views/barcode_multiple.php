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
    <link rel="stylesheet" href="<?= base_url('assets/css/paper.css'); ?>">
    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        @media print {

            html,
            body {
                height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }
        }
    </style>
    <style>
        .box {
            position: relative;
            width: 188px !important;
            height: 94px !important;
            border: 1px solid #ccc;
            text-align: center;
        }

        .multiple {
            float: left;
            margin: 10px 20px;
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

<body class="A4" onLoad="javascript:window.print();">
    <section class="sheet" style="padding: 5mm 10mm">
        <?php if ($jumlahData > 1) { ?>
            <?php foreach ($data as $row) { ?>
                <div class="box multiple">
                    <span class="rotated">** <?= $namaToko; ?> **</span>
                    <div class="barang"><?= character_limiter($row['nama_barang'], 15, '...'); ?></div>
                    <?php echo '<img src="data:image/png;base64,' . $barcode->getBarcodePNG($row['barcode'], $tipe, 1, 50, array(1, 1, 1), true) . '" alt="barcode"   />'; ?>
                    <div class="harga">Rp<?= Ribuan($row['harga_jual']); ?> / <?= $row['satuan_barang']; ?></div>

                </div>
            <?php } ?>
        <?php } else { ?>
            <?php
            for ($i = 1; $i <= $jumlah; $i++) { ?>
                <?php foreach ($data as $row) { ?>
                    <div class="box multiple">
                        <span class="rotated">** <?= $namaToko; ?> **</span>
                        <div class="barang"><?= character_limiter($row['nama_barang'], 15, '...'); ?></div>
                        <?php echo '<img src="data:image/png;base64,' . $barcode->getBarcodePNG($row['barcode'], $tipe, 1, 50, array(1, 1, 1), true) . '" alt="barcode"   />'; ?>
                        <div class="harga">Rp<?= Ribuan($row['harga_jual']); ?> / <?= $row['satuan_barang']; ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </section>
</body>

</html>