<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <v-card>
        <v-card-title>
            <h2 class="mb-2 font-weight-medium"><?= $title; ?></h2>
            <v-btn color="success" outlined class="ms-3" link href="<?= base_url('files/Format_Import_Barang_Excel.xlsx'); ?>" elevation="1">
                <v-icon>mdi-download</v-icon> Download Format
            </v-btn>
        </v-card-title>
        <v-card-text>
            <h2 class="mb-3">File Upload</h2>
            <template>
                <?php
                if (session()->getFlashdata('error')) {
                ?>
                    <v-alert text outlined color="deep-orange" icon="mdi-alert-octagon" dense>
                        <?= session()->getFlashdata('error') ?>
                    </v-alert>
                <?php } ?>

                <?php if (session()->getFlashdata('success')) { ?>
                    <v-alert text outlined outlined type="success" dense>
                        <?= session()->getFlashdata('success') ?>
                    </v-alert>
                <?php } ?>
                <form method="post" action="<?= base_url('excel/saveExcel'); ?>" enctype="multipart/form-data">
                    <v-checkbox name="ignorename" v-model="checkbox" label="Ignore the same Item Name" :value="checkbox"></v-checkbox>

                    <v-file-input show-size label="Upload File anda disini" id="file" name="fileexcel" class="mb-2" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" filled error-messages="<?= validation_show_error('fileexcel', $template = 'text') ?>"></v-file-input>
                    <v-btn large type="submit" color="primary" elevation="1">
                        <v-icon>mdi-upload</v-icon> Upload
                    </v-btn>
                </form>
            </template>
            <br />
            <h3 class="mb-3">Information:</h3>
            <ol>
                <li>Terdapat 15 kolom isian: <strong>Barcode, Nama Produk, Merk, Harga Beli, Harga Jual, Diskon, Kategori, Satuan, Satuan Nilai, Deskripsi, Stok, Stok Min, Supplier, Expired, SKU</strong></li>
                <li>Isikan value sesuai kolom di file excel, biarkan kosong jika tidak ada datanya seperti value: Barcode, Deskripsi, Supplier, SKU</li>
                <li>Isikan 0 pada Diskon &amp; Stok Min jika tidak ada data</li>
                <li>Harga Beli dan Harga Jual harus diisi, masukkan harga tanpa titik contoh (1000)</li>
                <li>Isikan harga tanpa titik contoh (1000) pada Diskon jika Ada diskon barang</li>
                <li>Isikan pada Supplier formatnya <strong>Nama - Perusahaan - Alamat</strong> (pisahkan dengan spasi dan -), contoh: Aksara - PT Aksara Karya Utama - Purwokerto, biarkan <strong>kosong</strong> jika barang tidak ada supplier</li>
                <li>Pada Expired ganti format pada excel ke tipe Date, contoh: 30/01/2025. Kosongkan kolom Expired jika tidak ada datanya</li>
            </ol>
        </v-card-text>
    </v-card>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    // Mendapatkan Token JWT
    const token = JSON.parse(localStorage.getItem('access_token'));

    // Menambahkan Auth Bearer Token yang didapatkan sebelumnya
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    // Initial Data
    dataVue = {
        ...dataVue,
        checkbox: true,

    }

    // Vue Created
    createdVue = function() {

    }

    // Vue Methods
    methodsVue = {
        ...methodsVue,

    }
</script>
<?php $this->endSection("js") ?>