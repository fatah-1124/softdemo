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
                        <p class="mb-0">Dari Tanggal - Sampai Tanggal</p>
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
            <v-btn color="primary" large dark href="<?= base_url('pembelian/baru') ?>" elevation="1">
                <v-icon>mdi-plus</v-icon> <?= lang('App.add') ?>
            </v-btn>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line clearable>
            </v-text-field>
        </v-card-title>
        <!-- Table Pembelian -->
        <v-data-table :headers="headers" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>" dense>
            <template v-slot:item="{ item }">
                <tr>
                    <td><a @click="showItem(item)">{{item.faktur}}</a></td>
                    <td>
                        <div v-for="list in dataVendor" :key="item.id_kontak" v-if="item.id_kontak != '0'">
                            <div v-if="item.id_kontak == list.id_kontak">
                                {{list.nama}}<br />
                                <span class="grey--text">{{list.perusahaan}}</span>
                            </div>
                        </div>
                        <span v-else>Tidak ada</span>
                    </td>
                    <td>{{dayjs(item.tanggal).format('DD-MM-YYYY')}}</td>
                    <td>{{dayjs(item.jatuh_tempo).format('DD-MM-YYYY')}}</td>
                    <td><span class="success--text" v-if="item.status_bayar == 1">Lunas</span><span class="error--text" v-else>Belum Dibayar</span></td>
                    <td>{{Ribuan(item.total ?? "0")}}</td>
                    <td>
                        <v-btn icon color="success" class="mr-3" v-if="item.status_bayar == '1'">
                            <v-icon>mdi-check-circle</v-icon>
                        </v-btn>
                        <v-btn icon color="primary" class="mr-3" @click="editItem(item)" v-else>
                            <v-icon>mdi-receipt-text</v-icon>
                        </v-btn>
                        <v-btn icon color="red" @click="deleteItem(item)">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table Pembelian -->
    </v-card>
</template>

<!-- Modal Show -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalShow" persistent scrollable max-width="900px">
            <v-card>
                <v-card-title>Detail Nomor Pembelian: {{faktur}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalShow = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-row>
                        <v-col>
                            <p class="mb-0 text-subtitle-1">Tanggal</p>
                            <h3 class="text-h6">{{dayjs(tanggal).format('DD-MM-YYYY')}}</h3>
                        </v-col>
                        <v-col>
                            <p class="mb-0 text-subtitle-1">Tanggal Jatuh Tempo</p>
                            <h3 class="text-h6">{{dayjs(jatuhTempo).format('DD-MM-YYYY')}}</h3>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <p class="mb-0 text-subtitle-1">SubTotal</p>
                            <h3 class="text-h6">Rp{{subtotal}}</h3>
                        </v-col>
                        <v-col>
                            <p class="mb-0 text-subtitle-1">Biaya</p>
                            <h3 class="text-h6">Rp{{biaya}}</h3>
                        </v-col>
                        <v-col>
                            <p class="mb-0 text-subtitle-1">Total</p>
                            <h3 class="text-h6">Rp{{total}}</h3>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <p class="mb-2 text-subtitle-1"><?= lang('App.listItems'); ?></p>
                            <v-data-table :headers="tbItemBeli" :items="itemBeli" :items-per-page="5" class="elevation-1" :loading="loading1">
                            </v-data-table>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent max-width="700px">
            <v-card>
                <v-card-title>Pembelian: {{faktur}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-alert type="warning" text outlined icon="mdi-alert-octagon">
                        <h3 class="text-h5 mb-3 grey--text text--darken-3">Tagihan: {{RibuanLocale(total)}}</h3>
                        <p class="grey--text text--darken-3">Warung/Toko anda memiliki Hutang Pembelian dengan No. Pembelian: {{faktur}} sejumlah tagihan: {{RibuanLocale(total)}}</p>
                    </v-alert>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" link :href="'<?= base_url('hutang'); ?>?faktur=' + faktur">
                        Proses <?= lang('App.payment') ?> <v-icon>mdi-arrow-right</v-icon>
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
                    <v-btn @click="modalDelete = false" large elevation="1"><?= lang('App.close'); ?></v-btn>
                    <v-btn color="error" dark @click="deletePembelian" :loading="loading" elevation="1" large><?= lang('App.delete'); ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

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
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        search: "<?= $search; ?>",
        menu: false,
        startDate: "<?= $startDate; ?>",
        endDate: "<?= $endDate; ?>",
        headers: [{
            text: 'Faktur',
            value: 'faktur'
        }, {
            text: 'Vendor/Supplier',
            value: 'id_kontak'
        }, {
            text: 'Tanggal',
            value: 'tanggal'
        }, {
            text: 'Jatuh Tempo',
            value: 'jatuh_tempo'
        }, {
            text: 'Status',
            value: 'status_bayar'
        }, {
            text: 'Total',
            value: 'total'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        dataVendor: [],
        dataPembelian: [],
        totalData: 0,
        data: [],
        options: {},
        idPembelian: "",
        faktur: "",
        statusBayar: "",
        itemBeli: [],
        tbItemBeli: [{
            text: 'Nama',
            value: 'nama_barang'
        }, {
            text: 'Qty',
            value: 'qty'
        }, {
            text: 'Satuan',
            value: 'satuan'
        }, {
            text: 'Total',
            value: 'jumlah'
        }],
        tanggal: "",
        jatuhTempo: "",
        subtotal: "",
        biaya: "",
        total: "",
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getPembelianFiltered();
        this.getVendor();
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

        dataPembelian: function() {
            if (this.dataPembelian != '') {
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

                let items = this.dataPembelian
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
                this.getPembelianFiltered();
                this.menu = false;
            } else {
                this.getPembelian();
                this.startDate = "";
                this.endDate = "";
                this.menu = false;
            }
        },

        // Get Pembelian
        getPembelian: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/pembelian?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPembelian = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPembelian = data.data;
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

        // Get Pembelian Filtered
        getPembelianFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/pembelian?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPembelian = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPembelian = data.data;
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

        // Get Show Item
        showItem: function(item) {
            this.modalShow = true;
            this.idPembelian = item.id_pembelian;
            this.faktur = item.faktur;
            this.tanggal = item.tanggal;
            this.jatuhTempo = item.jatuh_tempo;
            this.subtotal = item.subtotal;
            this.biaya = item.biaya;
            this.total = item.total;
            this.catatan = item.catatan;
            this.getItemPembelian();
        },

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.idPembelian = item.id_pembelian;
            this.faktur = item.faktur;
            this.total = item.total;
            this.statusBayar = '1';
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Get Item Pembelian
        getItemPembelian: function() {
            this.loading3 = true;
            axios.get(`<?= base_url(); ?>api/pembelian/item/${this.idPembelian}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.itemBeli = data.data;
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

        //Update
        update: function() {
            this.loading = true;
            axios.put(`<?= base_url(); ?>api/pembelian/update/${this.idPembelian}`, {
                    status_bayar: this.statusBayar,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPembelian();
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
                    this.loading = false;
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
            this.idPembelian = item.id_pembelian;
        },

        // Delete
        deletePembelian: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/pembelian/delete/${this.idPembelian}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPembelian();
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