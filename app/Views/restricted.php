<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <?php if (session()->getFlashdata('success')) { ?>
        <v-alert type="success" dismissible v-model="alert">
            <?= session()->getFlashdata('success') ?>
        </v-alert>
    <?php } ?>
    <div class="text-center">
        <h1>Restricted! Access Denied</h1>

        <v-icon color="green" size="256">mdi-shield-lock</v-icon>

        <h1>We are Sorry...</h1>
        <h2>The page you're trying to access has restricted access.<br />
            Please refer to your system administrator.</h2>
        <br />
        <v-btn color="primary" link href="<?= base_url('/dashboard'); ?>">Back to Dashboard</v-btn>
    </div>
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