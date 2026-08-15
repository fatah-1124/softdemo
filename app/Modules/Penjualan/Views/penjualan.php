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
    </h1>
    <v-card>
        <v-card-title>
            <v-btn color="primary" large dark href="<?= base_url('sales') ?>" elevation="1">
                <v-icon>mdi-cash-register</v-icon> <?= lang('App.add') ?>
            </v-btn>
            <v-spacer></v-spacer>
            <v-select v-model="search" label="Filter <?= lang('App.pay'); ?>" :items="dataBayar" item-text="text" item-value="value" prepend-icon="mdi-filter" @change="handleSubmit" single-line class="mr-3" style="max-width: 250px;"></v-select>
            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line clearable>
            </v-text-field>
        </v-card-title>
        <!--  <v-card-subtitle>
            <v-alert type="info" icon="mdi-cash-register" prominent text dense class="mb-0">
                <h3 class="font-weight-medium text-truncate mb-2 grey--text text--darken-3"><?= lang('App.transaction'); ?></h3>
                <h4 class="font-weight-regular grey--text text--darken-3"><?= lang('App.today'); ?>: <?= $countTrxHariini; ?>, Total: {{Ribuan(<?= ($totalTrxHariini - $sisaPiutangHariini) ?? "0"; ?>)}}*</h4>
                <h4 class="font-weight-regular grey--text text--darken-3"><?= lang('App.yesterday'); ?>: <?= $countTrxHarikemarin; ?>, Total: {{Ribuan(<?= ($totalTrxHarikemarin - $sisaPiutangHarikemarin) ?? "0"; ?>)}}*</h4>
            </v-alert>
        </v-card-subtitle> -->
        <!-- Table Penjualan -->
        <v-data-table v-model="selected" item-key="id_penjualan" show-select :headers="headers" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="<?= lang('App.loadingWait'); ?>" dense>
            <template v-slot:top>

            </template>
            <template v-slot:item="{ item, isSelected, select }">
                <tr :class="isSelected ? 'grey lighten-2':''">
                    <td @click="toggle(isSelected,select,$event)">
                        <v-icon color="primary" v-if="isSelected">mdi-checkbox-marked</v-icon>
                        <v-icon v-else>mdi-checkbox-blank-outline</v-icon>
                    </td>
                    <td><a link @click="editItem(item)">{{item.faktur}}</a></td>
                    <td>{{dayjs(item.created_at).format('DD-MM-YYYY HH:mm')}}</td>
                    <td>{{item.nama_kontak}}</td>
                    <td>{{item.jumlah}}</td>
                    <td>{{Ribuan(item.total)}}</td>
                    <td>
                        <v-chip label small>
                            <span class="text-uppercase font-weight-medium green--text" v-if="item.metode_bayar == 'cash'">{{item.metode_bayar}}</span>
                            <span class="text-uppercase font-weight-medium red--text" v-if="item.metode_bayar == 'credit'">{{item.metode_bayar}}</span>
                            <span class="text-uppercase font-weight-medium blue--text" v-if="item.metode_bayar == 'bank'">{{item.metode_bayar}}</span>
                        </v-chip>
                        <v-btn x-small color="error" class="py-3" link :href="'<?= base_url('piutang'); ?>?faktur=' + item.faktur" elevation="0" title="Piutang" alt="Piutang" v-if="item.id_piutang != null && item.status_piutang == '0'">
                            Unpaid <v-icon small>mdi-book-arrow-left</v-icon>
                        </v-btn>
                        <v-btn x-small color="success" class="py-3" link :href="'<?= base_url('piutang'); ?>?faktur=' + item.faktur" elevation="0" title="Piutang" alt="Piutang" v-else-if="item.id_piutang != null && item.status_piutang == '1'">
                            Paid <v-icon small>mdi-book-arrow-left</v-icon>
                        </v-btn><br />
                        Bayar: {{Ribuan(item.bayar)}}<br />
                        <span v-if="item.id_piutang != null && item.status_piutang == '0'">Kurang</span><span v-else>Kembali</span>: {{RibuanLocale(item.kembali)}}
                    </td>
                    <td>{{RibuanLocale(item.total_laba)}}</td>
                    <td>
                        <v-menu right bottom min-width="200px">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn text v-bind="attrs" v-on="on">
                                    <v-icon>mdi-printer</v-icon> Print
                                </v-btn>
                            </template>

                            <v-list dense>
                                <?php if ($cetakUSB == "1") : ?>
                                    <v-list-item @click="printUSB(item)">
                                        <v-list-item-icon class="me-3">
                                            <v-icon>mdi-usb-port</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Nota (USB*/Thermal)</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                                <?php if ($cetakBluetooth == "1") : ?>
                                    <v-list-item @click="printBT(item)">
                                        <v-list-item-icon class="me-3">
                                            <v-icon>mdi-bluetooth</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Nota (BT*/Thermal)</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                                <v-list-item @click="printHtml(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-printer-pos</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Nota (In-Browser/Thermal)</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="printInvoiceA4(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-printer-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Invoice (In-Browser/A4)</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="printSuratjalanA4(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-printer-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Surat Jalan (In-Browser/A4)</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <v-btn icon color="primary" class="mr-2" @click="showNota(item)">
                            <v-icon>mdi-receipt-text</v-icon>
                        </v-btn>

                        <v-btn icon color="red" @click="deleteItem(item)" title="Delete" alt="Delete">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
            <template slot="body.append">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td>{{ sumTotal('jumlah') }}</td>
                    <td>{{ RibuanLocale(sumTotal('total')) }}</td>
                    <td></td>
                    <td>{{ RibuanLocale(sumTotal('total_laba')) }}</td>
                    <td></td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table Nota -->
    </v-card>
</template>

<!-- Modal Nota -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalNota" persistent scrollable max-width="350px">
            <v-card class="pa-2">
                <v-card-title class="text-h5">
                    <?= lang('App.receipt') ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalNota = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <div class="mb-2 text-center" style="line-height: normal;">
                        <div class="d-flex justify-center mb-2">
                            <v-img :src="logo" max-width="50"></v-img>
                        </div>
                        <h4 class="text-display-1 mb-2">{{toko.nama_toko}}</h4>
                        <span class="text-display-2"><span v-show="toko.NIB != 0">NIB: {{toko.NIB}}<br /></span>
                            {{toko.alamat_toko}}<br />
                            Telp/WA: {{toko.telp}}
                        </span>
                    </div>
                    <v-divider></v-divider>
                    <div>
                        No: {{faktur}}<br />
                        Hr/Tgl: {{dayjs(tanggal).format('dddd, DD-MMM-YYYY HH:mm')}}<br />
                        Kasir: <?= session()->get('nama') ?><br />
                        Customer: {{kontak}}
                    </div>
                    <v-divider></v-divider>
                    <div v-for="item in itemPenjualan" :key="item.id_itempenjualan">
                        {{item.nama_barang}}<br />
                        {{item.qty}} ({{item.satuan}}) x {{RibuanLocaleNoRp(item.harga_jual_grosir == 0 ? item.harga_jual: (item.qty >= item.jumlah_min_grosir ? item.harga_jual_grosir:item.harga_jual))}}
                        <span class="float-right">{{RibuanLocaleNoRp(item.jumlah)}}</span>
                    </div>
                    <v-divider></v-divider>
                    <div>
                        Subtotal ({{jumlah}} item): <span class="float-right">{{RibuanLocaleNoRp(subtotal)}}</span><br />
                        PPN {{ppn}}%: <span class="float-right">{{RibuanLocaleNoRp(pajak)}}</span><br />
                        Diskon {{diskonPersen}}%: <span class="float-right">{{RibuanLocaleNoRp(diskon)}}</span><br />
                        <?php if ($pembulatan == 1) : ?>
                            Pembulatan: <span class="float-right">{{RibuanLocaleNoRp(pembulatan)}}</span><br />
                        <?php endif; ?>
                        <v-divider></v-divider>
                        <strong>Total: <span class="float-right">{{RibuanLocaleNoRp(total)}}</span></strong><br />
                        Bayar: <span class="float-right">{{RibuanLocaleNoRp(bayar)}}</span><br />
                        <span v-if="kembali >= '0'">
                            Kembali: <span class="float-right">{{RibuanLocaleNoRp(kembali ?? "0")}}</span><br />
                        </span>
                        <span v-else>
                            Kurang: <span class="float-right">{{RibuanLocaleNoRp(kembali ?? "0")}}</span><br />
                        </span>
                    </div>
                    <v-divider></v-divider>
                    <div class="mt-2 mb-0 text-center" style="font-size: 11px;line-height: normal;">{{toko.footer_nota}}. Dicetak menggunakan <strong>Aplikasi <?= env('appName') ?></strong> by <?= env('appCompany') ?></div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Nota -->

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
                    <v-btn color="error" dark @click="deleteNota" :loading="loading" elevation="1" large><?= lang('App.delete'); ?></v-btn>
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
    //RawBT
    function pc_print(data) {
        var socket = new WebSocket("ws://127.0.0.1:40213/");
        socket.bufferType = "arraybuffer";
        socket.onerror = function(error) {
            alert("Error! RawBT Websocket Server for PC not found");
        };
        socket.onopen = function() {
            socket.send(data);
            socket.close(1000, "Work complete");
        };
    }

    function android_print(data) {
        window.location.href = data;
        //alert("Print Bluetooth Success");
    }

    function ajax_print(url, btn) {
        $.get(url, function(data) {
            var ua = navigator.userAgent.toLowerCase();
            var isAndroid = ua.indexOf("android") > -1;
            if (isAndroid) {
                android_print(data);
            } else {
                pc_print(data);
            }
        });
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

    // Initial Data
    dataVue = {
        ...dataVue,
        search: "<?= $search; ?>",
        menu: false,
        startDate: "<?= $startDate; ?>",
        endDate: "<?= $endDate; ?>",
        headers: [{
            text: 'Faktur',
            value: 'faktur'
        }, {
            text: 'Tgl/Jam',
            value: 'created_at'
        }, {
            text: 'Customer',
            value: 'nama_kontak'
        }, {
            text: 'Item',
            value: 'jumlah'
        }, {
            text: 'Total*',
            value: 'total'
        }, {
            text: '<?= lang('App.pay'); ?>',
            value: 'metode_bayar'
        }, {
            text: 'Laba',
            value: 'total_laba'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        toko: [],
        dataPenjualan: [],
        itemPenjualan: [],
        totalData: 0,
        data: [],
        options: {},
        selected: [],
        faktur: "",
        idPenjualan: "",
        jumlah: "",
        ppn: 0,
        subtotal: 0,
        pajak: 0,
        diskon: 0,
        diskonPersen: 0,
        total: 0,
        bayar: 0,
        kembali: 0,
        kontak: "",
        modalAdd: false,
        modalEdit: false,
        modalNota: false,
        modalDelete: false,
        tanggal: "",
        logo: "<?= base_url() . '/' . $logo; ?>",
        pembulatan: 0,
        dataBayar: [{
            text: '<?= lang('App.payCash'); ?>',
            value: 'cash',
        }, {
            text: '<?= lang('App.credit'); ?>',
            value: 'credit',
        }, {
            text: 'Bank',
            value: 'bank',
        }],
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getPenjualanFiltered();
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

        dataPenjualan: function() {
            if (this.dataPenjualan != '') {
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

                let items = this.dataPenjualan
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

        toggle(isSelected, select, e) {
            select(!isSelected)
        },

        // Format Ribuan Rupiah versi 1
        RibuanLocale(key) {
            const rupiah = 'Rp' + Number(key).toLocaleString('id-ID');
            return rupiah
        },
        RibuanLocaleNoRp(key) {
            const rupiah = Number(key).toLocaleString('id-ID');
            return rupiah
        },

        // Format Ribuan Rupiah versi 2
        Ribuan(key) {
            // versi 1
            /* var number_string = key.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            } */

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
                this.getPenjualanFiltered();
                this.menu = false;
            } else {
                this.getPenjualan();
                this.startDate = "";
                this.endDate = "";
                this.menu = false;
            }
        },

        // Get Penjualan
        getPenjualan: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/penjualan?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
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

        // Get Penjualan Filtered
        getPenjualanFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/penjualan?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
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

        // Jumlah Total
        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.data.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            return sum
        },

        //Get Toko
        getToko: function() {
            axios.get(`<?= base_url(); ?>api/toko`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.toko = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.toko = data.data;
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

        //Show Nota
        showNota: function(item) {
            this.loading3 = true;
            this.modalNota = true;
            this.idPenjualan = item.id_penjualan;
            this.faktur = item.faktur;
            this.jumlah = item.jumlah;
            this.ppn = item.PPN;
            this.subtotal = item.subtotal;
            this.pajak = item.pajak;
            this.diskon = item.diskon;
            this.diskonPersen = item.diskon_persen;
            this.total = item.total;
            this.bayar = item.bayar;
            this.kembali = item.kembali;
            this.tanggal = item.created_at;
            this.kontak = item.nama_kontak;
            this.pembulatan = item.pembulatan;
            this.getToko();
            this.getItemPenjualan();
        },

        //Get Item Penjualan
        getItemPenjualan: function() {
            this.loading3 = true;
            axios.get(`<?= base_url(); ?>api/cetaknota/${this.idPenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.itemPenjualan = data.data;
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

        // Print USB
        printUSB: function(item) {
            this.loading4 = true;
            this.idPenjualan = item.id_penjualan;
            axios.post(`<?= base_url() ?>api/penjualan/cetakusb`, {
                    id_penjualan: this.idPenjualan
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        //setTimeout(() => window.open(data.data.url, '_blank'), 1000);
                        //this.$refs.form.resetValidation();
                        //this.$refs.form.reset();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        //this.$refs.form.validate();
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

        // Print Bluetooth
        printBT: function(item) {
            this.loading4 = true;
            this.idPenjualan = item.id_penjualan;
            axios.post(`<?= base_url() ?>api/penjualan/cetakbluetooth`, {
                    id_penjualan: this.idPenjualan
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;

                    // RawBT
                    var ua = navigator.userAgent.toLowerCase();
                    var isAndroid = ua.indexOf("android") > -1;
                    if (isAndroid) {
                        android_print(data);
                    } else {
                        pc_print(data);
                    }

                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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
        },

        // Get Item Delete
        deleteItem: function(item) {
            this.modalDelete = true;
            this.idPenjualan = item.id_penjualan;
        },

        // Delete
        deleteNota: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/penjualan/delete/${this.idPenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPenjualan();
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

        // Get Item Edit Barang
        editItem: function(item) {
            setTimeout(() => window.location.href = `<?= base_url('penjualan') ?>/${item.id_penjualan}/edit`, 100);
        },

        printHtml: function(item) {
            window.open('<?= base_url('penjualan/printnota-html?id_penjualan='); ?>' + item.id_penjualan, "_blank", "popup=yes");
        },

        printInvoiceA4: function(item) {
            window.open('<?= base_url('penjualan/print-invoice-a4?id_penjualan='); ?>' + item.id_penjualan, "_blank", "popup=yes");
        },

        printSuratjalanA4: function(item) {
            window.open('<?= base_url('penjualan/print-suratjalan-a4?id_penjualan='); ?>' + item.id_penjualan, "_blank", "popup=yes");
        }
    }
</script>
<?php $this->endSection("js") ?>