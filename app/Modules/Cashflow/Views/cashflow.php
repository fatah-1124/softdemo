<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title ?> &nbsp;<span class="font-weight-regular">{{startDate}} {{startDate != '' ? "&mdash;": ""}} {{endDate}}</span>
        <template>
            <v-menu v-model="menu" :close-on-content-click="false" offset-y>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-calendar-filter</v-icon>
                    </v-btn>
                </template>
                <v-card width="250">
                    <v-card-text>
                        <p class="mb-1"><strong>Filter:</strong></p>
                        <div class="mb-3">
                            <a @click="hariini" title="Hari Ini" alt="Hari Ini">Hari Ini</a> &bull;
                            <a @click="tujuhHari" title="7 Hari Kemarin" alt="7 Hari Kemarin">7 Hari terakhir</a> &bull;
                            <a @click="satuBulan" title="30 Hari terakhir" alt="30 Hari terakhir">30 Hari terakhir</a> &bull;
                            <a @click="bulanIni" title="Bulan Ini" alt="Bulan Ini">Bulan Ini</a> &bull;
                            <a @click="tigaBulan" title="3 Bulan Lalu" alt="3 Bulan Lalu">3 Bulan terakhir</a> &bull;
                            <a @click="tahunIni" title="Tahun Ini" alt="Tahun Ini">Tahun Ini</a> &bull;
                            <a @click="tahunLalu" title="Tahun Lalu" alt="Tahun Lalu">Tahun Lalu</a> &bull;
                            <a @click="reset" title="Reset" alt="Reset">Reset</a>
                        </div>
                        <p class="mb-1"><strong>Custom:</strong></p>
                        <p class="mb-1">Dari Tanggal - Sampai Tanggal</p>
                        <v-text-field v-model="startDate" type="date"></v-text-field>
                        <v-text-field v-model="endDate" type="date"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn text @click="menu = false">
                            <?= lang('App.close'); ?>
                        </v-btn>
                        <v-btn color="primary" text @click="handleSubmit" :loading="loading">
                            Filter
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-menu>
        </template>
        <strong class="float-end">{{RibuanLocale(saldo)}}</strong>
    </h1>
    <v-card>
        <v-card-title>
            <v-btn large color="primary" dark @click="modalAddOpen" elevation="1">
                <v-icon>mdi-plus</v-icon> <?= lang('App.add'); ?>
            </v-btn>
            <v-spacer></v-spacer>
            <v-select v-model="search" label="Filter Jenis" :items="dataJenis" item-text="text" item-value="value" prepend-icon="mdi-filter" @change="handleSubmit" clearable class="mr-3" style="max-width: 250px;"></v-select>

            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line clearable>
            </v-text-field>
        </v-card-title>
        <!-- Table Kas -->
        <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>">
            <!-- <template v-slot:top>
                
            </template> -->
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.faktur}}</td>
                    <td>{{dayjs(item.tanggal).format('DD-MM-YYYY')}} {{item.waktu}}</td>
                    <td>{{item.jenis}}</td>
                    <td>{{item.kategori}}</td>
                    <td>{{RibuanLocale(item.pemasukan)}}</td>
                    <td>{{RibuanLocale(item.pengeluaran)}}</td>
                    <td width="200">
                        {{item.keterangan}}
                        <v-btn icon color="primary" class="mr-3" link :href="'<?= base_url('biaya'); ?>?faktur=' + item.faktur" title="" alt="" v-if="item.id_biaya != null">
                            <v-icon>mdi-wallet</v-icon>
                        </v-btn>

                        <v-btn icon color="primary" class="mr-3" link :href="'<?= base_url('penjualan'); ?>?faktur=' + item.faktur" title="" alt="" v-else-if="item.id_penjualan != null">
                            <v-icon>mdi-receipt-text</v-icon>
                        </v-btn>

                        <v-btn icon color="primary" class="mr-3" link :href="'<?= base_url('pembelian'); ?>?faktur=' + item.faktur" title="" alt="" v-else-if="item.id_pembelian != null">
                            <v-icon>mdi-cart</v-icon>
                        </v-btn>
                    </td>
                    <td>{{item.nama}}</td>
                    <td>
                        <v-btn icon color="primary" @click="editItem(item)" class="mr-2" title="Edit" alt="Edit">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon color="error" @click="deleteItem(item)" title="Delete" alt="Delete" :disabled="item.id_penjualan != null || item.id_pembelian != null || item.id_biaya != null">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table -->
    </v-card>
</template>

<!-- Modal Add -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent max-width="600px">
            <v-card>
                <v-card-title><?= lang('App.add') ?> <?= lang('App.cashflow'); ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form v-model="valid" ref="form">
                        <v-row>
                            <v-col>
                                <v-menu ref="date" v-model="date" :close-on-content-click="false" transition="scale-transition">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="tanggal" label="Pilih Tanggal" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="tanggalError" outlined></v-text-field>
                                    </template>
                                    <v-date-picker v-model="tanggal" @input="date = false" color="primary"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col>
                                <v-menu ref="time" v-model="time" :close-on-content-click="false" :return-value.sync="waktu" transition="scale-transition" offset-y max-width="290px" min-width="290px">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="waktu" label="Pilih Waktu" prepend-inner-icon="mdi-clock-time-four-outline" readonly v-bind="attrs" v-on="on" :error-messages="waktuError" outlined></v-text-field>
                                    </template>
                                    <v-time-picker v-if="time" v-model="waktu" full-width @click:minute="$refs.time.save(waktu)" format="24hr"></v-time-picker>
                                </v-menu>
                            </v-col>
                        </v-row>

                        <v-select v-model="jenis" label="<?= lang('App.type'); ?>" :items="dataJenis" item-text="text" item-value="value" :error-messages="jenisError" outlined></v-select>

                        <v-select v-model="kategori" label="<?= lang('App.category'); ?>" :items="dataKategori" item-text="text" item-value="value" :error-messages="kategoriError" outlined></v-select>

                        <v-text-field v-model="nominal" type="number" label="Nominal (Rp)" :error-messages="nominalError" outlined></v-text-field>

                        <v-textarea v-model="keterangan" label="<?= lang('App.description'); ?>" :error-messages="keteranganError" rows="3" outlined></v-textarea>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveCashflow" :loading="loading1" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Add -->

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent max-width="600px">
            <v-card>
                <v-card-title><?= lang('App.edit') ?> <?= $title; ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-text-field v-model="faktur" label="No. Faktur" filled disabled></v-text-field>

                        <v-row>
                            <v-col>
                                <v-menu ref="date" v-model="date" :close-on-content-click="false" transition="scale-transition">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="tanggal" label="Pilih Tanggal" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="tanggalError" outlined></v-text-field>
                                    </template>
                                    <v-date-picker v-model="tanggal" @input="date = false" color="primary"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col>
                                <v-menu ref="time" v-model="time" :close-on-content-click="false" :return-value.sync="waktu" transition="scale-transition" offset-y max-width="290px" min-width="290px">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="waktu" label="Pilih Waktu" prepend-inner-icon="mdi-clock-time-four-outline" readonly v-bind="attrs" v-on="on" :error-messages="waktuError" outlined></v-text-field>
                                    </template>
                                    <v-time-picker v-if="time" v-model="waktu" full-width @click:minute="$refs.time.save(waktu)" format="24hr"></v-time-picker>
                                </v-menu>
                            </v-col>
                        </v-row>

                        <v-select v-model="jenis" label="<?= lang('App.type'); ?>" :items="dataJenis" item-text="text" item-value="value" :error-messages="jenisError" outlined disabled></v-select>

                        <v-select v-model="kategori" label="<?= lang('App.category'); ?>" :items="dataKategori" item-text="text" item-value="value" :error-messages="kategoriError" outlined disabled></v-select>

                        <v-text-field v-model="nominal" type="number" label="Nominal (Rp)" :error-messages="nominalError" outlined disabled></v-text-field>

                        <v-textarea v-model="keterangan" label="<?= lang('App.description'); ?>" :error-messages="keteranganError" rows="3" outlined></v-textarea>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateCashflow" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.update') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Modal Delete -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalDelete" persistent max-width="600px">
            <v-card class="pa-2">
                <v-card-title>
                    <v-icon color="error" class="mr-2" x-large>mdi-alert-octagon</v-icon> <?= lang('App.confirmDelete'); ?>
                </v-card-title>
                <v-card-text>
                    <div class="mt-5 py-5">
                        <h2 class="font-weight-regular"><?= lang('App.delConfirm'); ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="modalDelete = false" large elevation="1"><?= lang('App.close'); ?></v-btn>
                    <v-btn color="red" dark @click="deleteCashflow" :loading="loading" elevation="1" large><?= lang('App.delete'); ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

<v-dialog v-model="loading2" hide-overlay persistent width="300">
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
    };

    // Deklarasi errorKeys
    var errorKeys = []

    // Initial Data
    dataVue = {
        ...dataVue,
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        modalShow: false,
        search: "<?= $search; ?>",
        menu: false,
        startDate: "<?= $startDate; ?>",
        endDate: "<?= $endDate; ?>",
        dataCashflow: [],
        menu: false,
        totalData: 0,
        data: [],
        options: {},
        dataTable: [{
            text: 'Faktur',
            value: 'faktur'
        }, {
            text: 'Tanggal',
            value: 'tanggal'
        }, {
            text: 'Jenis',
            value: 'jenis'
        }, {
            text: 'Ketegori',
            value: 'kategori'
        }, {
            text: 'Pemasukan',
            value: 'pemasukan'
        }, {
            text: 'Pengeluaran',
            value: 'pangeluaran'
        }, {
            text: 'Keterangan',
            value: 'keterangan'
        }, {
            text: 'User',
            value: 'id_login'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        idCashflow: "",
        faktur: "",
        date: false,
        time: false,
        tanggal: "",
        tanggalError: "",
        waktu: "",
        waktuError: "",
        jenis: "",
        jenisError: "",
        kategori: "",
        kategoriError: "",
        dataJenis: [{
            text: 'Pemasukan',
            value: 'Pemasukan'
        }, {
            text: 'Pengeluaran',
            value: 'Pengeluaran'
        }, {
            text: 'Mutasi ke Bank',
            value: 'Mutasi ke Bank'
        }],
        dataKategori: [{
            text: 'Penjualan',
            value: 'Penjualan'
        }, {
            text: 'Pembelian',
            value: 'Pembelian'
        }, {
            text: 'Operasional',
            value: 'Operasional'
        }, {
            text: 'Lain-lain',
            value: 'Lainnya'
        }],
        nominal: "",
        nominalError: "",
        keterangan: "",
        keteranganError: "",
        saldo: 0,
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getCashflowFiltered();
        this.getSaldo();
    }

    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataCashflow: function() {
            if (this.dataCashflow != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        }
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        // Server-side paginate and sort
        getDataFromApi() {
            this.loading = true
            this.fetchData().then(data => {
                this.data = data.items
                this.totalData = data.total
                this.loading = false
            })
        },
        fetchData() {
            return new Promise((resolve, reject) => {
                const {
                    sortBy,
                    sortDesc,
                    page,
                    itemsPerPage
                } = this.options

                let search = this.search ?? "".trim();

                let items = this.dataCashflow
                const total = items.length

                if (search == search.toLowerCase()) {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .toLowerCase()
                            .includes(search);
                    });
                } else {
                    items = items.filter(item => {
                        return Object.values(item)
                            .join(",")
                            .includes(search);
                    });
                }

                if (sortBy.length === 1 && sortDesc.length === 1) {
                    items = items.sort((a, b) => {
                        const sortA = a[sortBy[0]]
                        const sortB = b[sortBy[0]]

                        if (sortDesc[0]) {
                            if (sortA < sortB) return 1
                            if (sortA > sortB) return -1
                            return 0
                        } else {
                            if (sortA < sortB) return -1
                            if (sortA > sortB) return 1
                            return 0
                        }
                    })
                }

                if (itemsPerPage > 0) {
                    items = items.slice((page - 1) * itemsPerPage, page * itemsPerPage)
                }

                setTimeout(() => {
                    resolve({
                        items,
                        total,
                    })
                }, 100)
            })
        },
        // End Server-side paginate and sort


        // Format Ribuan Rupiah versi 1
        RibuanLocale(key) {
            const rupiah = 'Rp' + Number(key).toLocaleString('id-ID');
            return rupiah
        },

        // Format Ribuan Rupiah versi 2
        Ribuan(key) {
            const format = key.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
            return rupiah;
        },

        // Modal Add Open
        modalAddOpen: function() {
            this.modalAdd = true;
            this.tanggal = "";
            this.waktu = "";
            this.jenis = "";
            this.kategori = "";
            this.nominal = "";
            this.keterangan = "";
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        // Filter Date
        reset: function() {
            this.startDate = "";
            this.endDate = "";
        },
        tujuhHari: function() {
            this.startDate = "<?= $tujuhHari; ?>";
            this.endDate = "<?= $kemarin; ?>";
        },
        hariini: function() {
            this.startDate = "<?= $hariini; ?>";
            this.endDate = "<?= $hariini; ?>";
        },
        bulanIni: function() {
            this.startDate = "<?= $awalBulan; ?>";
            this.endDate = "<?= $akhirBulan; ?>";
        },
        satuBulan: function() {
            this.startDate = "<?= $satuBulanAwal; ?>";
            this.endDate = "<?= $satuBulanAkhir; ?>";
        },
        tigaBulan: function() {
            this.startDate = "<?= $tigaBulanAwal; ?>";
            this.endDate = "<?= $tigaBulanAkhir; ?>";
        },
        tahunIni: function() {
            this.startDate = "<?= $awalTahun; ?>";
            this.endDate = "<?= $akhirTahun; ?>";
        },
        tahunLalu: function() {
            this.startDate = "<?= $awalTahunLalu; ?>";
            this.endDate = "<?= $akhirTahunLalu; ?>";
        },

        // Handle Submit Filter
        handleSubmit: function() {
            if (this.startDate != '' && this.endDate != '') {
                this.getCashflowFiltered();
                this.getSaldo();
                this.menu = false;
            } else {
                this.getCashflow();
                this.getSaldo();
                this.startDate = "";
                this.endDate = "";
                this.menu = false;
            }
        },

        // Get Kas
        getCashflow: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/cashflow`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataCashflow = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataCashflow = data.data;
                        this.data = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Kas Filtered
        getCashflowFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/cashflow?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataCashflow = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataCashflow = data.data;
                        this.data = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Saldo 
        getSaldo: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/cashflow/saldo`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.saldo = parseInt(data.data);
                        //console.log(this.saldo);
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.saldo = data.data;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Save Kas
        saveCashflow: function() {
            this.loading1 = true;
            axios.post('<?= base_url(); ?>api/cashflow/save', {
                    jenis: this.jenis,
                    tanggal: this.tanggal,
                    waktu: this.waktu,
                    kategori: this.kategori,
                    nominal: parseInt(this.nominal),
                    keterangan: this.keterangan
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCashflow();
                        this.getSaldo();
                        this.tanggal = "";
                        this.waktu = "";
                        this.jenis = "";
                        this.kategori = "";
                        this.nominal = "";
                        this.keterangan = "";
                        this.modalAdd = false;
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
                        this.modalAdd = true;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            if (item.jenis == 'Pemasukan') {
                var nominal = item.pemasukan;
            } else {
                var nominal = item.pengeluaran;
            }
            this.idCashflow = item.id_cashflow;
            this.faktur = item.faktur;
            this.tanggal = item.tanggal;
            this.waktu = item.waktu;
            this.jenis = item.jenis;
            this.kategori = item.kategori;
            this.nominal = nominal;
            this.keterangan = item.keterangan;
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateCashflow: function() {
            this.loading = true;
            axios.put(`<?= base_url(); ?>api/cashflow/update/${this.idCashflow}`, {
                    tanggal: this.tanggal,
                    waktu: this.waktu,
                    jenis: this.jenis,
                    kategori: this.kategori,
                    nominal: parseInt(this.nominal),
                    keterangan: this.keterangan
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCashflow();
                        this.tanggal = "";
                        this.waktu = "";
                        this.jenis = "";
                        this.kategori = "";
                        this.nominal = "";
                        this.keterangan = "";
                        this.modalEdit = false;
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
                        this.modalEdit = true;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Item Delete
        deleteItem: function(item) {
            this.modalDelete = true;
            this.idCashflow = item.id_cashflow;
        },

        // Delete
        deleteCashflow: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/cashflow/delete/${this.idCashflow}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getCashflow();
                        this.getSaldo();
                        this.modalDelete = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalDelete = true;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
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