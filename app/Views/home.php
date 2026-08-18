<?php $this->extend("layouts/frontend"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container :class="$vuetify.theme.dark ? '':'indigo lighten-5'" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm8 md5>
                <v-card>
                    <v-card-text class="text-center">
                        <h1>Sistem Kasir dan Manajemen Stok</h1><br>
                        <h2>Toko ANA</h2> 
                        <h3>Desa Klino, Kecamatan Sekar, Bojonegoro</h3> 
                        <br>
                        <div>
                            <img src="<?=base_url('images/minimart.jpg') ?>" alt="foto" width="500" class="flex">
                        </div>
                        <br>
                        <p>
                            Klik tombol login di pojok kanan atas untuk memulai. <br>
                            Email: admin@gmail.com, password: 12345678.
                        </p>
                    </v-card-text>
                    
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>  
<?php $this->endSection("js") ?>