<?php $this->extend("layouts/backend"); ?>
<?php $this->section("styles"); ?>
<style>

</style>
<?php $this->endSection("styles") ?>
<?php $this->section("content"); ?>
<template>
    <v-card>
        <v-toolbar elevation="1">
            <v-btn icon href="<?= base_url('pembelian') ?>">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title class="font-weight-medium"><?= $title; ?></v-toolbar-title>
        </v-toolbar>
        <v-card-text>
            <v-form ref="form" v-model="valid">
                <p class="mb-2 text-subtitle-1">Vendor/Supplier/Kulakan</p>
                <v-autocomplete v-model="idKontak" label="Pilih Kontak" :items="dataVendor" :item-text="dataVendor =>`${dataVendor.nama} - ${dataVendor.perusahaan} - ${dataVendor.telepon}`" item-value="id_kontak" prepend-inner-icon="mdi-account" :error-messages="id_kontakError" :loading="loading3" outlined :disabled="idKontak == '0'">
                    <template v-slot:prepend-item>
                        <v-subheader class="mt-n3 mb-n3">{{ dataVendor.length }} Vendor/Supplier/Kulakan found</v-subheader>
                    </template>
                </v-autocomplete>
                <v-checkbox v-model="noVendor" @change="clicknoVendor" label="No Vendor/Supplier/Kulakan" class="mt-n2"></v-checkbox>

                <v-row>
                    <v-col cols="12" sm="6" class="mb-n4">
                        <p class="mb-2 text-subtitle-1">Tanggal Transaksi</p>
                        <v-menu ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition" offset-y min-width="auto">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="tanggal" label="Tanggal Hari ini" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="tanggalError" outlined></v-text-field>
                            </template>
                            <v-date-picker v-model="tanggal" @input="menu = false" color="primary"></v-date-picker>
                        </v-menu>
                    </v-col>

                    <v-col cols="12" sm="6">
                        <p class="mb-2 text-subtitle-1">Jatuh Tempo</p>
                        <v-menu ref="menu2" v-model="menu2" :close-on-content-click="false" transition="scale-transition" offset-y min-width="auto">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field v-model="jatuhTempo" label="Tanggal Jatuh Tempo" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="jatuh_tempoError" outlined></v-text-field>
                            </template>
                            <v-date-picker v-model="jatuhTempo" @input="menu2 = false" color="primary"></v-date-picker>
                        </v-menu>
                    </v-col>
                </v-row>
            </v-form>

            <p class="mb-2 text-subtitle-1"><?= lang('App.listItems'); ?></p>
            <v-autocomplete v-model="idBarang" label="Pilih <?= lang('App.items'); ?>/Barcode/SKU" :items="barang" :item-text="barang =>`${barang.nama_barang} - Rp${barang.harga_beli} - Stok: ${barang.stok} - Barcode: ${barang.barcode} - SKU: ${barang.sku}`" item-value="id_barang" :disabled="idKontak == ''" :loading="loading3" outlined>
                <template v-slot:append-outer>
                    <v-btn icon color="primary" x-large @click="selectBarang" :disabled="idKontak == ''" style="bottom: 15px !important;">
                        <v-icon x-large>mdi-content-save-plus</v-icon>
                    </v-btn>
                </template>
            </v-autocomplete>

            <v-data-table height="300" :headers="tbKeranjang" :fixed-header="true" :items="dataKeranjang" item-key="id_order" :loading="loading" loading-text="<?= lang('App.loadingWait'); ?>">
                <template v-slot:item="{ item }">
                    <tr>
                        <td width="50">{{item.index}}</td>
                        <td>{{item.nama_barang}}</td>
                        <td>
                            <v-edit-dialog large persistent :return-value.sync="item.harga_beli" @save="setHargaBeli(item)" @cancel="" @open="" @close="">
                                {{ Ribuan(item.harga_beli) }}
                                <template v-slot:input>
                                    <v-text-field v-model="item.harga_beli" type="number" class="pt-3" append-icon="mdi-content-save" @click:append="setHargaBeli(item)" outlined dense hide-details single-line></v-text-field>
                                </template>
                            </v-edit-dialog>
                        </td>
                        <td>
                            <v-edit-dialog large persistent :return-value.sync="item.harga_jual" @save="setHargaJual(item)" @cancel="" @open="" @close="">
                                <div v-if="item.diskon > 0"><span class="text-decoration-line-through">{{ Ribuan(item.harga_jual) }}</span>
                                    <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">{{item.diskon_persen}}%</v-chip><br />{{ Ribuan(item.harga_jual - item.diskon) }}
                                </div>
                                <div v-else>{{ Ribuan(item.harga_jual) }}</div>
                                <template v-slot:input>
                                    <v-text-field v-model="item.harga_jual" type="number" class="pt-3" append-icon="mdi-content-save" @click:append="setHargaJual(item)" outlined dense hide-details single-line :disabled="item.diskon > 0"></v-text-field>
                                </template>
                            </v-edit-dialog>
                        </td>
                        <td>{{item.satuan}}</td>
                        <td width="200">
                            <v-text-field v-model="item.qty" type="number" single-line prepend-icon="mdi-minus" append-outer-icon="mdi-plus" @click:append-outer="increment(item)" @click:prepend="decrement(item)" @input="setQty(item)" min="0" hide-details></v-text-field>
                        </td>
                        <td>{{Ribuan(item.jumlah)}}</td>
                        <td>
                            <v-btn icon @click="hapusItem(item)" title="Delete" alt="Delete">
                                <v-icon color="red">
                                    mdi-delete
                                </v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
                <template slot="footer.prepend">
                    <v-btn color="error" @click="modalDelete = true" title="<?= lang('App.resetCart'); ?>" elevation="1" :disabled="keranjang == '' ? true:false">
                        Reset <v-icon>mdi-cart</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card-text>

        <v-card-text class="mt-n5">
            <v-row>
                <v-col cols="12" sm="6">
                    <v-textarea v-model="catatan" label="Catatan (Keterangan Biaya, dll)" rows="4"></v-textarea>

                    <v-alert type="info" border="right" colored-border elevation="1" class="text-caption">
                        <h3 class="text-subtitle-1">
                            Information
                        </h3>
                        1. Lunas = Bayar Ambil Dari Kas<br />
                        2. Belum Dibayar = Masuk Hutang<br />
                        3. Harga berubah? Klik pada tabel Harga Beli/Jual untuk merubah Harga
                    </v-alert>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-row class="mt-0">
                        <v-col cols="4">
                            <v-subheader>Sub Total</v-subheader>
                        </v-col>
                        <v-col cols="8">
                            <v-text-field label="Jumlah Rp" :value="sumSubTotal('jumlah')" readonly></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row class="mt-n3">
                        <v-col cols="4">
                            <v-subheader>Biaya Lainnya</v-subheader>
                        </v-col>
                        <v-col cols="8">
                            <v-text-field label="Biaya Rp" v-model="biayaLainnya" type="number" :rules="[rules.number]"></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row class="mt-n3">
                        <v-col cols="4">
                            <v-subheader>Total</v-subheader>
                        </v-col>
                        <v-col cols="8">
                            <v-text-field label="Total Rp" v-model="total" :error-messages="totalError" readonly></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row class="mt-n5">
                        <v-col cols="4">
                            <v-subheader class="mt-2">Status Pembayaran (Bayar dari Kas)</v-subheader>
                        </v-col>
                        <v-col cols="8">
                            <v-checkbox v-model="statusBayar" false-value="0" true-value="1" :label="`${statusBayar == '1' ? 'Lunas':'Belum Dibayar'}`"></v-checkbox>
                        </v-col>
                    </v-row>

                    <v-row class="mt-3">
                        <v-col cols="12">
                            <v-btn large block color="primary" @click="savePembelian" title="<?= lang('App.save') ?>" :loading="loading2" elevation="1">
                                <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>


</template>

<!-- Modal Reset Keranjang -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title>
                    <v-icon color="error" class="mr-2" x-large>mdi-alert-octagon</v-icon> Konfirmasi
                </v-card-title>
                <v-card-text>
                    <div class="mt-5 py-5">
                        <h2 class="font-weight-regular"><?= lang('App.confirmResetCart') ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large text @click="modalDelete = false"><?= lang('App.no') ?></v-btn>
                    <v-btn large color="error" dark @click="resetKeranjang" :loading="loading2" elevation="1">
                        <?= lang('App.delete') ?>
                    </v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Reset -->

<v-dialog v-model="loading4" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
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
    }

    // Deklarasi errorKeys
    var errorKeys = []

    // Initial Data
    dataVue = {
        ...dataVue,
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        menu: false,
        menu2: false,
        scan: "",
        pencarian: "",
        dataVendor: [],
        idKontak: "",
        id_kontakError: "",
        tanggal: "<?= date('Y-m-d'); ?>",
        tanggalError: "",
        jatuhTempo: "",
        jatuh_tempoError: "",
        catatan: "",
        catatanError: "",
        statusBayar: "1",
        keranjang: [],
        itemkeranjang: [],
        barang: [],
        selectedBarang: [],
        idBarang: "",
        idKeranjang: "",
        qty: 0,
        jumlah: 0,
        subtotal: 0,
        biayaLainnya: 0,
        total: "",
        totalError: "",
        tbKeranjang: [{
            text: '',
            value: 'id_order'
        }, {
            text: 'Nama',
            value: 'nama_barang'
        }, {
            text: 'Harga Beli',
            value: 'harga_beli'
        }, {
            text: 'Harga Jual',
            value: 'harga_jual'
        }, {
            text: 'Satuan',
            value: 'satuan'
        }, {
            text: 'Qty',
            value: 'qty'
        }, {
            text: 'Jumlah',
            value: 'jumlah'
        }, {
            text: '',
            value: 'actions'
        }, ],
        alert: false,
        hargaBeli: "",
        harga_beliError: "",
        hargaJual: "",
        noVendor: false
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getBarang();
        this.getVendor();
        this.resetKeranjang();
        setTimeout(() => this.getKeranjang(), 1000);
    }

    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
        dataKeranjang() {
            return this.keranjang.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    watchVue = {
        ...watchVue,
        alert: function() {
            if (this.alert == true) {
                window.onbeforeunload = function() {
                    return "Data will be lost if you leave the page, are you sure?";
                };
            } else {
                window.onbeforeunload = null;
            }
        },
        subtotal: function() {
            const total = parseInt(this.biayaLainnya) + parseInt(this.subtotal);
            this.total = total;
        },
        biayaLainnya: function() {
            const total = parseInt(this.biayaLainnya) + parseInt(this.subtotal);
            this.total = total;
        },
        idKontak: function() {
            this.getBarang();
        },
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        // Modal Open
        clicknoVendor: function() {
            if (this.noVendor == true) {
                this.idKontak = "0";
            } else {
                this.idKontak = "";
            }
        },

        // Format Ribuan Rupiah versi 2
        Ribuan(key) {
            const format = key.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
            return rupiah;
        },

        // Modal Open
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
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

        // Get Barang
        getBarang: function() {
            this.loading4 = true;
            axios.get(`<?= base_url(); ?>api/barang/beli/${this.idKontak}`, options)
                .then(res => {
                    // handle success
                    this.loading4 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.barang = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.barang = data.data;
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

        // Scan Barang
        selectBarang: function() {
            this.loading4 = true;
            if (this.idBarang.length < 1) {
                this.snackbar = true;
                this.snackbarMessage = "Anda belum memilih 1 <?= lang('App.items'); ?>";
                this.loading4 = false;
            } else {
                axios.get(`<?= base_url(); ?>api/barang/${this.idBarang}`, options)
                    .then(res => {
                        // handle success
                        this.loading4 = false;
                        var data = res.data;
                        if (data.status == true) {
                            this.snackbar = true;
                            this.snackbarMessage = data.message;
                            this.selectedBarang = data.data;
                            this.saveKeranjang(this.selectedBarang);
                        } else {
                            this.snackbar = true;
                            this.snackbarMessage = data.message;
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
            }
        },

        // Get Keranjang
        getKeranjang: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/keranjang/beli`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.keranjang = data.data;
                        const itemkeranjang = this.keranjang.map((row) => (
                            [row.id_barang, row.harga_beli, row.stok, row.qty, row.satuan, row.harga_jual]
                        ));
                        this.itemkeranjang = itemkeranjang;
                        //console.log(this.itemkeranjang);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.keranjang = data.data;
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

        // Save Keranjang
        saveKeranjang: function(item) {
            this.loading4 = true;
            axios.post(`<?= base_url(); ?>api/keranjang/beli/save`, {
                    id_barang: item.id_barang,
                    harga_beli: item.harga_beli,
                    stok: item.stok,
                    qty: 1,
                    id_kontak: this.idKontak,
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.alert = true;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.alert = false;
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

        // Reset Keranjang
        resetKeranjang: function() {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/keranjang/beli/reset`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.modalDelete = false;
                        this.alert = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.alert = false;
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

        // Delete Item Keranjang
        hapusItem: function(item) {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/keranjang/beli/delete/${item.id_order}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.alert = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.alert = false;
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

        // Sum Total
        sumSubTotal(key) {
            // sum data in give key (property)
            let subtotal = 0
            const sum = this.keranjang.reduce((accumulator, currentValue) => {
                return (subtotal += +currentValue[key])
            }, 0)
            this.subtotal = sum;
            return sum
        },

        increment(item) {
            item.qty++;
            if (item.qty < 0) return;
            this.setQty(item);
        },
        decrement(item) {
            item.qty--;
            if (item.qty < 0) {
                item.qty = 0;
            } else {
                this.setQty(item);
            };
        },

        // Set Qty Item
        setQty: function(item) {
            this.loading = true;
            this.idKeranjang = item.id_order;
            this.qty = item.qty;
            this.idBarang = item.id_barang;
            this.hargaBeli = item.harga_beli;
            this.hargaJual = item.harga_jual;
            axios.put(`<?= base_url(); ?>api/keranjang/beli/update/${this.idKeranjang}`, {
                    id_barang: this.idBarang,
                    harga_beli: this.hargaBeli,
                    harga_jual: this.hargaJual,
                    qty: this.qty,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
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
                        this.getKeranjang();
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

        // Save Pembelian
        savePembelian: function(item) {
            this.loading2 = true;
            const data = this.itemkeranjang;
            //console.log(data);
            axios.post(`<?= base_url(); ?>api/pembelian/save`, {
                    data: data,
                    tanggal: this.tanggal,
                    jatuh_tempo: this.jatuhTempo,
                    subtotal: this.subtotal,
                    biaya: this.biayaLainnya,
                    total: this.total,
                    id_kontak: this.idKontak,
                    catatan: this.catatan,
                    status_bayar: parseInt(this.statusBayar),
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.alert = false;
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
                    this.loading2 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Harga Beli
        setHargaBeli: function(item) {
            this.loading = true;
            this.idKeranjang = item.id_order;
            this.idBarang = item.id_barang;
            this.hargaBeli = item.harga_beli;
            axios.put(`<?= base_url(); ?>api/barang/sethargabeli/${this.idBarang}`, {
                    harga_beli: this.hargaBeli,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getBarang();
                        this.setQty(item);
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
                        this.getKeranjang();
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

        // Set Item Harga Jual
        setHargaJual: function(item) {
            this.loading = true;
            this.idKeranjang = item.id_order;
            this.idBarang = item.id_barang;
            this.hargaJual = item.harga_jual;
            axios.put(`<?= base_url(); ?>api/barang/sethargajual/${this.idBarang}`, {
                    harga_jual: this.hargaJual,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getBarang();
                        this.setQty(item);
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
                        this.getKeranjang();
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