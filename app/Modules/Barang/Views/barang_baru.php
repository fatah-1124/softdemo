<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <v-card>
        <v-toolbar elevation="1">
            <v-btn icon href="<?= base_url('barang') ?>">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title class="font-weight-medium"><?= lang('App.addItem') ?></v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn color="primary" dark large @click="saveBarang" :loading="loading">
                <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
            </v-btn>
        </v-toolbar>
        <v-card-text>
            <v-form ref="form" v-model="valid">
                <v-row class="mt-n4">
                    <v-col class="mb-n10" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.itemImg') ?></p>
                        <p class="text-caption">Format gambar .jpg .jpeg .png </p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-file-input v-model="image" show-size label="Image Upload" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" @click:clear="onFileClear" :loading="loading2" v-if="imagePreview == null"></v-file-input>

                        <div v-else>
                            <v-img :src="imagePreview" max-width="200">
                                <v-overlay v-model="overlay" absolute :opacity="0.1">
                                    <v-btn small class="ma-2" color="error" dark @click="deleteMedia">
                                        Hapus
                                        <v-icon dark right>
                                            mdi-delete
                                        </v-icon>
                                    </v-btn>
                                </v-overlay>
                            </v-img>
                        </div>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n7" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold">Barcode / SKU</p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-row>
                            <v-col cols="12" sm="6" class="mb-n3">
                                <v-text-field label="Barcode" v-model="barcode" :error-messages="barcodeError" outlined dense></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field label="SKU" v-model="sku" :error-messages="skuError" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.itemName') ?></p>
                        <p class="text-caption">Cantumkan min. 3 karakter agar mudah ditemukan terdiri dari jenis barang, merek, dan keterangan seperti warna, bahan, atau tipe.</p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-text-field label="<?= lang('App.inputItemName') ?> *" v-model="namaBarang" :error-messages="nama_barangError" outlined></v-text-field>
                    </v-col>
                </v-row>

                <v-row class="">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.category') ?> / <?= lang('App.unit') ?></p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-row>
                            <v-col cols="12" sm="5" class="mb-n3">
                                <v-select v-model="idKategori" label="<?= lang('App.selectCategory'); ?>" :items="dataKategori" item-text="nama_kategori" item-value="id_kategori" :error-messages="id_kategoriError" :loading="loading2" outlined dense append-outer-icon="mdi-plus-thick" @click:append-outer="addKategori"></v-select>
                            </v-col>
                            <v-col cols="12" sm="4" class="mb-n3">
                                <v-select v-model="idSatuan" label="Pilih <?= lang('App.unit') ?>" :items="dataSatuan" item-text="nama_satuan" item-value="id_satuan" :error-messages="satuan_barangError" :loading="loading2" outlined dense @change="getSatuanById" append-outer-icon="mdi-plus-thick" @click:append-outer="addSatuan"></v-select>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field label="Nilai Satuan Minimal" v-model="satuanNilai" type="number" :error-messages="satuan_nilaiError" outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.merk') ?></p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <div class="mb-3">
                            <v-btn value="Tidak ada merk" @click="btnMerk($event)" small rounded elevation="0">Tidak ada merk</v-btn>
                            <span v-if="loading4 == false">
                                <v-btn v-for="item in dataMerk" :value="item.merk" @click="btnMerk($event)" small rounded elevation="0">{{item.merk}}</v-btn>
                            </span>
                            <span v-else><?= lang('App.loadingWait'); ?></span>
                            <a @click="getRandomMerk" title="Reload" alt="Reload">&nbsp;Reload</a>
                        </div>
                        <v-text-field label="<?= lang('App.merk') ?>" v-model="merk" :error-messages="merkError" outlined dense></v-text-field>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.itemDesc') ?></p>
                        <p class="text-caption">Pastikan deskripsi barang memuat spesifikasi, ukuran, bahan, masa berlaku, dan lainnya.</p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-textarea v-model="deskripsi" counter maxlength="3000" outlined full-width rows="4" auto-grow :error-messages="deskripsiError"></v-textarea>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-2 text-subtitle-1 font-weight-bold"><?= lang('App.price') . ' ' . lang('App.unit'); ?></p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-row class="mb-n7">
                            <v-col cols="12" sm="4">
                                <v-text-field label="<?= lang('App.priceBuy') ?> *" v-model="hargaBeli" type="number" :error-messages="harga_beliError" prefix="Rp" @input="countHargajual" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3">
                                <v-text-field label="x Margin %" v-model="marginBarang" type="number" @input="countHargajual" :loading="loading5" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="5">
                                <v-text-field label="<?= lang('App.priceSell') ?> *" v-model="hargaJual" type="number" :error-messages="harga_jualError" prefix="Rp" dense outlined></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-checkbox v-model="checkDiskon" label="Aktifkan Diskon <?= lang('App.priceSell') ?>" class="mt-n3"></v-checkbox>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field v-model="diskon" label="<?= lang('App.discount') ?> (Rp)" type="number" :error-messages="diskonError" prefix="Rp" :suffix=" diskonPersen.toFixed() + '%'" @focus="$event.target.select()" dense outlined v-show="checkDiskon == true"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-2 text-subtitle-1 font-weight-bold"><?= lang('App.price') . ' ' . lang('App.wholesale'); ?></p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-checkbox v-model="checkGrosir" label="Aktifkan <?= lang('App.wholesale'); ?>" class="mt-1"></v-checkbox>
                        <v-row class="mb-n7" v-show="checkGrosir == true">
                            <v-col cols="12" sm="5">
                                <v-text-field label="<?= lang('App.minQty') ?> *" v-model="jumlahMinGrosir" type="number" :error-messages="jumlah_min_grosirError" min="0" prefix="&ge;" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="5">
                                <v-text-field label="<?= lang('App.wholesalePrice') ?> *" v-model="hargaJualGrosir" type="number" :error-messages="harga_jual_grosirError" prefix="Rp" dense outlined></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n8" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold">Status Barang</p>
                        <p class="text-caption">Jika status aktif, barangmu dapat dibeli oleh pembeli.</p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-switch v-model="active" name="active" false-value="0" true-value="1" color="success" :label="active == false ? 'Tidak Aktif':'Aktif'"></v-switch>
                    </v-col>
                </v-row>

                <v-row class="mt-0">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold"><?= lang('App.stock') ?></p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-text-field label="1000" v-model="stok" type="number" :error-messages="stokError" single-line outlined dense></v-text-field>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-row class="mt-n5">
                            <v-col cols="12" md="5">
                                <span class="text-subtitle-1 font-weight-bold mb-0"><?= lang('App.stock') ?> Min</span>
                            </v-col>
                            <v-col cols="12" md="7">
                                <v-text-field label="0" v-model="stokMin" type="number" :error-messages="stok_minError" single-line outlined dense></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <v-row class="mt-0">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold">Vendor/Supplier</p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-autocomplete v-model="idKontak" label="Pilih Kontak" :items="dataVendor" :item-text="dataVendor =>`${dataVendor.nama} - ${dataVendor.perusahaan} - ${dataVendor.telepon}`" item-value="id_kontak" prepend-inner-icon="mdi-account" :error-messages="id_kontakError" :loading="loading3" outlined clearable dense></v-autocomplete>
                    </v-col>
                </v-row>

                <v-row class="mt-n4">
                    <v-col class="mb-n5" cols="12" md="3">
                        <p class="mb-1 text-subtitle-1 font-weight-bold">Expired</p>
                        <p class="text-caption"></p>
                    </v-col>
                    <v-col cols="12" md="9">
                        <v-text-field type="date" label="Expired" v-model="expired" :error-messages="expiredError" outlined dense></v-text-field>
                    </v-col>
                </v-row>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<!-- Modal Kategori -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalKategori" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title>
                    Kategori
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalKategoriClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="7">
                                    <v-text-field label="<?= lang('App.categoryName') ?>" v-model="namaKategori" type="text" :error-messages="nama_kategoriError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="5">
                                    <v-btn color="primary" large @click="saveKategori" :loading="loading2"><?= lang('App.add') ?></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                    <v-data-table :headers="tbKategori" :items="dataKategori" :items-per-page="5" class="elevation-1" :loading="loading1">
                        <template v-slot:item="{ item }">
                            <tr>
                                <td>{{item.id_kategori}}</td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nama_kategori" @save="updateKategori(item)" @cancel="" @open="" @close="">
                                        {{item.nama_kategori}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nama_kategori" class="pt-3" append-icon="mdi-content-save" @click:append="updateKategori(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-btn color="error" icon @click="deleteKategori(item)" :loading="loading3">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalKategoriClose" elevation="1"><?= lang('App.close') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<!-- Modal Satuan -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalSatuan" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title>
                    Satuan
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalSatuanClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="5">
                                    <v-text-field label="Nama Satuan" v-model="namaSatuan" type="text" :error-messages="nama_satuanError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-text-field label="Nilai Satuan" v-model="nilaiSatuan" type="number" :error-messages="nilai_satuanError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="2">
                                    <v-btn color="primary" large @click="saveSatuan" :loading="loading2"><?= lang('App.add') ?></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                    <v-data-table :headers="tbSatuan" :items="dataSatuan" :items-per-page="5" class="elevation-1" :loading="loading1">
                        <template v-slot:item="{ item }">
                            <tr>
                                <td>{{item.id_satuan}}</td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nama_satuan" @save="updateSatuan(item)" @cancel="" @open="" @close="">
                                        {{item.nama_satuan}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nama_satuan" class="pt-3" append-icon="mdi-content-save" @click:append="updateSatuan(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nilai_satuan" @save="updateSatuan(item)" @cancel="" @open="" @close="">
                                        {{item.nilai_satuan}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nilai_satuan" class="pt-3" append-icon="mdi-content-save" @click:append="updateSatuan(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-btn color="error" icon @click="deleteSatuan(item)" :loading="loading3">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalSatuanClose" elevation="1"><?= lang('App.close') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<!-- Loading2 -->
<v-dialog v-model="loading2" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<!-- -->
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    // Base64-to-Blob Digunakan dalam method Upload
    function b64toBlob(b64Data, contentType, sliceSize) {
        contentType = contentType || '';
        sliceSize = sliceSize || 512;

        var byteCharacters = atob(b64Data);
        var byteArrays = [];

        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            var slice = byteCharacters.slice(offset, offset + sliceSize);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }

        var blob = new Blob(byteArrays, {
            type: contentType
        });
        return blob;
    }

    // Mendapatkan Token JWT
    const token = JSON.parse(localStorage.getItem('access_token'));

    // Menambahkan Auth Bearer Token yang didapatkan sebelumnya
    const options = {
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    };

    // Deklarasi errorKeys
    var errorKeys = []

    // Initial Data
    dataVue = {
        ...dataVue,
        modalKategori: false,
        dataKategori: [],
        modalSatuan: false,
        dataSatuan: [],
        idBarang: "<?= $uuid ?>",
        barcode: "",
        barcodeError: "",
        idKategori: "",
        id_kategoriError: "",
        sku: null,
        skuError: "",
        namaBarang: "",
        nama_barangError: "",
        merk: "",
        merkError: "",
        hargaBeli: "",
        harga_beliError: "",
        hargaJual: "",
        harga_jualError: "",
        checkDiskon: false,
        diskon: 0,
        diskonError: "",
        diskonPersen: 0,
        satuanBarang: "",
        satuan_barangError: "",
        satuanNilai: "",
        satuan_nilaiError: "",
        deskripsi: "",
        deskripsiError: "",
        stok: "",
        stokError: "",
        active: "1",
        idKontak: null,
        id_kontakError: "",
        stokMin: "",
        stok_minError: "",
        expired: null,
        expiredError: "",
        idMedia: "",
        mediaPath: null,
        image: null,
        imagePreview: null,
        overlay: false,
        namaKategori: "",
        namaKategoriEdit: "",
        nama_kategoriError: "",
        tbKategori: [{
            text: 'ID',
            value: 'id_kategori'
        }, {
            text: 'Nama Kategori',
            value: 'nama_kategori'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        idSatuan: "",
        id_satuanError: "",
        namaSatuan: "",
        namaSatuanEdit: "",
        nama_satuanError: "",
        nilaiSatuan: "",
        nilaiSatuanEdit: "",
        nilai_satuanError: "",
        tbSatuan: [{
            text: 'ID',
            value: 'id_satuan'
        }, {
            text: 'Satuan',
            value: 'nama_satuan'
        }, {
            text: 'Nilai',
            value: 'nilai_satuan'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        dataVendor: [],
        untungPersen: 0,
        dataMerk: [],
        marginBarang: "<?= $marginBarang; ?>",
        checkGrosir: false,
        jumlahMinGrosir: 0,
        jumlah_min_grosirError: "",
        hargaJualGrosir: 0,
        harga_jual_grosirError: ""
    }

    // Vue Created
    createdVue = function() {
        this.getKategori();
        this.getSatuan();
        this.getVendor();
        this.getRandomMerk();
    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    watchVue = {
        ...watchVue,
        diskon: function() {
            if (Number(this.diskon) > Number(this.hargaJual)) {
                this.diskonError = "Diskon terlalu besar";
                //setTimeout(() => this.diskonError = "", 4000);
            } else {
                this.diskonError = "";
            }

            if (Number(this.diskon) == 0) {
                this.diskonPersen = 0;
            }

            if (Number(this.diskon) > 0) {
                let hitung = Number(this.hargaJual) - Number(this.diskon)
                let persen = Number(this.hargaJual) - hitung
                this.diskonPersen = (persen / Number(this.hargaJual)) * 100;
            }
        },

        hargaJualGrosir: function() {
            if (Number(this.hargaJualGrosir) >= Number(this.hargaJual)) {
                this.harga_jual_grosirError = "Harga grosir harus lebih murah dari harga jual satuan";
            } else {
                this.harga_jual_grosirError = "";
            }
        }
    }

    // Vue Methods
    methodsVue = {
        ...methodsVue,
        // Upload Browse File
        onFileChange() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image)
            reader.onload = e => {
                this.imagePreview = e.target.result;
                this.uploadFile(this.imagePreview);
            }
        },

        // Upload Clear File
        onFileClear() {
            this.image = null;
            this.imagePreview = null;
            this.overlay = false;
            this.snackbar = true;
            this.snackbarMessage = 'Gambar berhasil dihapus';
            this.deleteMedia();
        },

        // Start Upload File
        uploadFile: function(file) {
            var formData = new FormData() // Split the base64 string in data and contentType
            var block = file.split(";"); // Get the content type of the image
            var contentType = block[0].split(":")[1]; // In this case "image/gif" get the real base64 content of the file
            var realData = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."

            // Convert it to a blob to upload
            var blob = b64toBlob(realData, contentType);
            formData.append('image', blob);
            formData.append('id_barang', this.idBarang);
            this.loading2 = true;
            axios.post(`<?= base_url() ?>api/media/save`, formData, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.idMedia = data.data
                        this.overlay = true;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image = null;
                        this.imagePreview = null;
                        this.overlay = false;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading2 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Delete Media
        deleteMedia: function() {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/media/delete/${this.idMedia}`, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.image = null;
                        this.imagePreview = null;
                        this.overlay = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Hitung Harga jual x margin %
        countHargajual: function() {
            let persen = (this.marginBarang / 100) * Number(this.hargaBeli);
            this.hargaJual = Number(this.hargaBeli) + persen;
        },

        // Get Random Merk
        getRandomMerk: function() {
            this.loading4 = true;
            axios.get('<?= base_url(); ?>api/barang/random/merk', options)
                .then(res => {
                    // handle success
                    this.loading4 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataMerk = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataMerk = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading4 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Button Merk
        btnMerk: function(e) {
            this.merk = e.target.textContent;
        },

        // Get Kategori
        getKategori: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>api/kategori', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataKategori = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataKategori = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading1 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Modal Kategori
        addKategori: function() {
            this.modalKategori = true;
        },
        modalKategoriClose: function() {
            this.modalKategori = false;
            this.$refs.form.resetValidation();
        },

        // Save Kategori
        saveKategori: function() {
            this.loading2 = true;
            axios.post(`<?= base_url(); ?>api/kategori/save`, {
                    nama_kategori: this.namaKategori,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.namaKategori = "";
                        this.getKategori();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading2 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Update Kategori
        updateKategori: function(item) {
            this.loading1 = true;
            this.namaKategoriEdit = item.nama_kategori;
            axios.put(`<?= base_url(); ?>api/kategori/update/${item.id_kategori}`, {
                    nama_kategori: this.namaKategoriEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKategori();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                            this.snackbarMessage = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Delete Kategori
        deleteKategori: function(item) {
            this.loading3 = true;
            axios.delete(`<?= base_url(); ?>api/kategori/delete/${item.id_kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKategori();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading3 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Satuan
        getSatuan: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>api/satuan', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataSatuan = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataSatuan = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading1 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Satuan
        getSatuanById: function() {
            this.loading1 = true;
            axios.get(`<?= base_url(); ?>api/satuan/${this.idSatuan}`, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.satuanBarang = data.data.nama_satuan;
                        this.satuanNilai = data.data.nilai_satuan;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.namaSatuan = data.data.nama_satuan;
                        this.nilaiSatuan = data.data.nilai_satuan;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading1 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Modal Satuan
        addSatuan: function() {
            this.modalSatuan = true;
        },
        modalSatuanClose: function() {
            this.modalSatuan = false;
            this.$refs.form.resetValidation();
        },

        // Save Satuan
        saveSatuan: function() {
            this.loading2 = true;
            axios.post(`<?= base_url(); ?>api/satuan/save`, {
                    nama_satuan: this.namaSatuan,
                    nilai_satuan: this.nilaiSatuan
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.namaSatuan = "";
                        this.nilaiSatuan = "";
                        this.getSatuan();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading2 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Update Satuan
        updateSatuan: function(item) {
            this.loading1 = true;
            this.namaSatuanEdit = item.nama_satuan;
            this.nilaiSatuanEdit = item.nilai_satuan;
            axios.put(`<?= base_url(); ?>api/satuan/update/${item.id_satuan}`, {
                    nama_satuan: this.namaSatuanEdit,
                    nilai_satuan: this.nilaiSatuanEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getSatuan();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                            this.snackbarMessage = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                    }

                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Delete Satuan
        deleteSatuan: function(item) {
            this.loading3 = true;
            axios.delete(`<?= base_url(); ?>api/satuan/delete/${item.id_satuan}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getSatuan();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading3 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Save Barang
        saveBarang: function() {
            this.loading = true;
            if (this.checkDiskon == false) {
                var diskon = 0;
            } else {
                var diskon = this.diskon;
            }
            if (this.checkGrosir == false) {
                var jumlahMinGrosir = 0;
                var hargaJualGrosir = 0;
            } else {
                var jumlahMinGrosir = this.jumlahMinGrosir;
                var hargaJualGrosir = this.hargaJualGrosir;
            }
            axios.post(`<?= base_url(); ?>api/barang/save`, {
                    uuid_barang: this.idBarang,
                    barcode: this.barcode,
                    id_kategori: this.idKategori,
                    sku: this.sku,
                    nama_barang: this.namaBarang,
                    merk: this.merk,
                    harga_beli: this.hargaBeli,
                    harga_jual: this.hargaJual,
                    diskon: diskon,
                    satuan_barang: this.satuanBarang,
                    satuan_nilai: this.satuanNilai,
                    deskripsi: this.deskripsi,
                    stok: this.stok,
                    active: this.active,
                    id_kontak: this.idKontak,
                    stok_min: this.stokMin,
                    expired: this.expired,
                    margin: this.marginBarang,
                    jumlah_min_grosir: jumlahMinGrosir,
                    harga_jual_grosir: hargaJualGrosir,
                    id_media: this.idMedia
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    if (this.harga_jual_grosirError != "" && this.checkGrosir == true) return;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.$refs.form.resetValidation();
                        setTimeout(() => window.location.href = data.data.url, 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        errorKeys = Object.keys(data.data);
                        errorKeys.map((el) => {
                            this[`${el}Error`] = data.data[el];
                        });
                        if (errorKeys.length > 0) {
                            setTimeout(() => this.notifType = "", 4000);
                            setTimeout(() => errorKeys.map((el) => {
                                this[`${el}Error`] = "";
                            }), 4000);
                        }
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Vendor
        getVendor: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/kontak/vendor', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataVendor = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataVendor = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },
    }
</script>
<?php $this->endSection("js") ?>