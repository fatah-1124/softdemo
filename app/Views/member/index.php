<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<v-card>
    <v-card-title>
    Contoh Halaman User
    </v-card-title>
    <v-card-text>
        <h1>Ini Contoh Halaman User</h1>
    </v-card-text>
</v-card>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?> 
<script>
    const token = JSON.parse(localStorage.getItem('access_token'));
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };
    
    dataVue = {
        ...dataVue,
    }

    createdVue = function() {
       
    }

    methodsVue = {
        ...methodsVue,
        
    }
</script>
<?php $this->endSection("js") ?>