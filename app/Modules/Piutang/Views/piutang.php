<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title; ?> &nbsp;<span class="font-weight-regular">{{startDate}} {{startDate != '' ? "&mdash;": ""}} {{endDate}}</span>
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
    </h1>
    <v-card>
        <v-card-title>
            <v-icon class="mr-3">mdi mdi-filter</v-icon>
            <v-select v-model="selectStatus" label="Filter Status" :items="dataStatus" item-text="text" item-value="value" attach multiple hide-details style="max-width: 300px;"></v-select>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable>
            </v-text-field>
        </v-card-title>
        <!-- Table -->
        <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>">
            <template v-slot:top>

            </template>
            <template v-slot:item="{ item }">
                <tr>
                    <td><a :href="'<?= base_url('penjualan'); ?>?faktur=' + item.faktur">{{item.faktur}}</a></td>
                    <td>{{item.nama_kontak}} <i>({{item.keterangan}})</i></td>
                    <td>{{dayjs(item.tanggal).format('DD-MM-YYYY')}}</td>
                    <td>{{dayjs(item.jatuh_tempo).format('DD-MM-YYYY')}}</td>
                    <td>{{RibuanLocale(item.jumlah_piutang)}}</td>
                    <td>{{RibuanLocale(item.jumlah_bayar)}}</td>
                    <td>{{RibuanLocale(item.sisa_piutang)}}</td>
                    <td><span class="success--text" v-if="item.status_piutang == 1">Lunas</span><span class="error--text" v-else>Belum Lunas</span></td>
                    <td>
                        <v-btn icon color="primary" class="mr-3" @click="editItem(item)" title="<?= lang('App.payment'); ?>" alt="<?= lang('App.payment'); ?>">
                            <v-icon>mdi-receipt-text-check</v-icon>
                        </v-btn>

                        <span v-if="item.jumlah_bayar != 0 || item.status_hutang == 1">
                            <v-btn icon color="error" title="Delete" alt="Delete" disabled>
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </span>
                        <span v-else>
                            <v-btn icon color="error" @click="deleteItem(item)" title="Delete" alt="Delete">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </span>
                    </td>
                </tr>
            </template>
            <template slot="body.append">
                <tr>
                    <td class="text-right">Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{RibuanLocale(sumTotal('jumlah_piutang'))}}</td>
                    <td>{{RibuanLocale(sumTotal('jumlah_bayar'))}}</td>
                    <td>{{RibuanLocale(sumTotal('sisa_piutang'))}}</td>
                    <td></td>
                    <td></td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table -->
    </v-card>
</template>

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent max-width="1000px">
            <v-card>
                <v-card-title>Pembayaran Piutang {{noPenjualan}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-row>
                        <v-col cols="12" md="5">
                            <v-card>
                                <v-card-title>
                                    Form Payment
                                </v-card-title>
                                <v-divider></v-divider>
                                <v-card-text class="mt-4">
                                    <v-form v-model="valid" ref="form">
                                        <v-text-field v-model="sisaPiutang" type="number" label="Sisa Hutang (Rp)" outlined disabled></v-text-field>

                                        <v-text-field v-model="nominal" type="number" label="Nominal (Rp)" :error-messages="nominalError" min="0" outlined></v-text-field>

                                        <v-btn large color="primary" @click="savePiutangBayar" :loading="loading2" :disabled="nominalError != '' || nominal == 0">
                                            <v-icon>mdi-content-save</v-icon> <?= lang('App.pay') ?>
                                        </v-btn>
                                    </v-form>
                                    <br />
                                    <v-alert type="info" border="right" colored-border elevation="1" class="text-caption">
                                        <strong>Informasi</strong> Pembayaran Masuk Kas
                                    </v-alert>
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="7">
                            <h2 class="font-weight-medium mb-3"><?= lang('App.paymentHistory'); ?></h2>
                            <v-skeleton-loader class="mx-auto" type="table-tbody" v-if="loading3 == true"></v-skeleton-loader>
                            <v-simple-table fixed-header height="400px" v-else>
                                <template v-slot:default>
                                    <thead>
                                        <tr>
                                            <th class="text-left">
                                                Tanggal
                                            </th>
                                            <th class="text-left">
                                                Nominal
                                            </th>
                                            <th class="text-left">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in dataPiutangBayar" :key="item.id_piutang_bayar">
                                            <td>{{dayjs(item.created_at).format('DD-MM-YYYY HH:mm')}}</td>
                                            <td>{{ RibuanLocale(item.nominal) }}</td>
                                            <td>
                                                <v-btn icon color="red" @click="deleteBayarItem(item)">
                                                    <v-icon>mdi-delete</v-icon>
                                                </v-btn>
                                            </td>
                                        </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalEditClose">
                        <?= lang('App.close') ?>
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
                    <v-btn color="red" dark @click="deletePiutang" :loading="loading4" elevation="1" large v-if="deleteId == 'piutang'"><?= lang('App.delete'); ?></v-btn>
                    <v-btn color="red" dark @click="deletePiutangBayar" :loading="loading4" elevation="1" large v-else-if="deleteId = 'pembayaran'"><?= lang('App.delete'); ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

<v-dialog v-model="loading5" hide-overlay persistent width="300">
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
        dataPiutang: [],
        totalData: 0,
        data: [],
        options: {},
        dataTable: [{
            text: 'Penjualan',
            value: 'faktur'
        }, {
            text: 'Customer',
            value: 'nama_kontak'
        }, {
            text: 'Tgl.Piutang',
            value: 'tanggal'
        }, {
            text: 'Jatuh Tempo',
            value: 'jatuh_tempo'
        }, {
            text: 'Jml.Piutang',
            value: 'jumlah_piutang'
        }, {
            text: 'Jml.Bayar',
            value: 'jumlah_bayar'
        }, {
            text: 'Sisa',
            value: 'sisa_piutang'
        }, {
            text: 'Status',
            value: 'status_piutang'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        idPiutang: "",
        noPenjualan: "",
        jumlahBayar: "",
        sisaPiutang: "",
        statusPiutang: "",
        selectStatus: [],
        dataStatus: [{
            text: 'Lunas',
            value: 1
        }, {
            text: 'Belum Lunas',
            value: 0
        }, ],
        nominal: "",
        nominalError: "",
        total: 0,
        dataPiutangBayar: [],
        idPiutangBayar: "",
        deleteId: ""
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getPiutangFiltered();
        this.getTotal();
    }


    // Vue Watch
    // Watch: Sebuah objek dimana keys adalah expresi-expresi untuk memantau dan values adalah callback-nya (fungsi yang dipanggil setelah suatu fungsi lain selesai dieksekusi).
    var watchVue = {
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataPiutang: function() {
            if (this.dataPiutang != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        },
         
        selectStatus: function() {
            if (this.selectStatus.length >= 1) {
                this.getPiutangStatus();
            } else {
                this.getPiutang();
            }
        },

        nominal: function() {
            if (parseInt(this.nominal) > parseInt(this.sisaPiutang)) {
                this.nominalError = "Nominal terlalu besar dari Sisa Piutang";
            } else {
                this.nominalError = "";
            }
        }
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
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

        // Filter Date
        reset: function() {
            this.startDate = "<?= $awalTahun; ?>";
            this.endDate = "<?= $akhirTahun; ?>";
            this.selectStatus = "";
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
            if (this.selectStatus != '') {
                this.getPiutangStatus();
            } else {
                this.getPiutang();
            }

            if (this.startDate != '' && this.endDate != '') {
                this.getPiutangFiltered();
                this.menu = false;
            } else {
                this.getPiutang();
                this.startDate = "";
                this.endDate = "";
                this.menu = false;
            }
        },

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

                let items = this.dataPiutang
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

        // Get Piutang
        getPiutang: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/piutang`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                        this.data = data.data;
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

        // Get Piutang Filtered
        getPiutangFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/piutang?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                        this.data = data.data;
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

        // Get Piutang
        getPiutangStatus: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/piutang?tgl_start=${this.startDate}&tgl_end=${this.endDate}&status=${this.selectStatus}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                        this.data = data.data
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

        // Jumlah Total
        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.data.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            return sum
        },

        // Sum Total Piutang
        sumTotalPiutang() {
            // sum data in give key (property)
            return this.dataPiutang.reduce(function(total, item) {
                return this.total = total + parseInt(item.jumlah_piutang);
            }, 0);
        },

        // Sum Total Piutang Bayar
        sumTotalPiutangBayar() {
            // sum data in give key (property)
            return this.dataPiutang.reduce(function(total, item) {
                return this.totalBayar = total + parseInt(item.jumlah_bayar);
            }, 0);
        },

        // Sum Total Sisa Piutang
        sumTotalSisaPiutang() {
            // sum data in give key (property)
            return this.dataPiutang.reduce(function(total, item) {
                return this.totalSisa = total + parseInt(item.sisa_piutang);
            }, 0);
        },

        // Get Total
        getTotal: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/piutang/total', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.total = data.data;
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

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.idPiutang = item.id_piutang;
            this.noPenjualan = item.faktur;
            this.jumlahPiutang = item.jumlah_piutang;
            this.jumlahBayar = item.jumlah_bayar;
            this.sisaPiutang = item.sisa_piutang;
            this.statusPiutang = item.status_piutang;
            this.getPiutangBayar();
        },

        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
            this.getPiutang();
        },

        //Get Pembayaran Piutang
        getPiutangBayar: function() {
            this.loading3 = true;
            axios.get(`<?= base_url(); ?>api/piutang/${this.idPiutang}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPiutangBayar = data.data;
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

        // Save Pembayaran Piutang
        savePiutangBayar: function() {
            this.loading2 = true;
            axios.post('<?= base_url(); ?>api/piutang/save', {
                    id_piutang: this.idPiutang,
                    faktur: this.noPenjualan,
                    sisa_piutang: this.sisaPiutang,
                    nominal: parseInt(this.nominal),
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPiutangBayar();
                        this.sisaPiutang = data.data.sisa_piutang;
                        this.nominal = "";
                        this.modalEdit = true;
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
                    this.loading2 = false;
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
            this.idPiutang = item.id_piutang;
            this.deleteId = "piutang";
        },

        // Delete Piutang
        deletePiutang: function() {
            this.loading4 = true;
            axios.delete(`<?= base_url() ?>api/piutang/delete/${this.idPiutang}`, options)
                .then(res => {
                    // handle success
                    this.loading4 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPiutang();
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
                    this.loading4 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Item Delete
        deleteBayarItem: function(item) {
            this.modalDelete = true;
            this.idPiutangBayar = item.id_piutang_bayar;
            this.deleteId = "pembayaran";
        },

        // Delete Pembayaran Piutang
        deletePiutangBayar: function() {
            this.loading4 = true;
            axios.delete(`<?= base_url() ?>api/piutang/bayar/delete/${this.idPiutangBayar}`, options)
                .then(res => {
                    // handle success
                    this.loading4 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPiutangBayar();
                        this.sisaPiutang = data.data.sisa_piutang;
                        this.modalDelete = false;
                        //this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalDelete = true;
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
    }
</script>
<?php $this->endSection("js") ?>