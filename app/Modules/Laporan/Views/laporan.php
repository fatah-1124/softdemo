<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-3"><?= $title; ?> &nbsp;<span class="font-weight-regular">{{startDate}} {{startDate != '' ? "&mdash;": ""}} {{endDate}}</span>
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
    <p><?= lang('App.reportDesc'); ?>.</p>

    <v-tabs>
        <v-tab>
            <?= lang('App.cash'); ?>
        </v-tab>
        <v-tab>
            <?= lang('App.sales'); ?>
        </v-tab>
        <?php if (in_array('viewLaporanLabaRugi', $permissions)) : ?>
            <v-tab>
                <?= lang('App.profitLoss'); ?>
            </v-tab>
        <?php endif; ?>
        <v-tab>
            <?= lang('App.items'); ?>
        </v-tab>
        <v-tab>
            <?= lang('App.stock'); ?>
        </v-tab>
        <v-tab>
            <?= lang('App.category'); ?>
        </v-tab>
        <v-tab>
            Log
        </v-tab>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_cash.php'); ?>
            </v-card>
        </v-tab-item>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_penjualan.php'); ?>
            </v-card>
        </v-tab-item>

        <?php if (in_array('viewLaporanLabaRugi', $permissions)) : ?>
            <v-tab-item>
                <v-card outlined>
                    <?= $this->include('App\Modules\Laporan\Views/laporan_labarugi.php'); ?>
                </v-card>
            </v-tab-item>
        <?php endif; ?>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_barang.php'); ?>
            </v-card>
        </v-tab-item>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_stok.php'); ?>
            </v-card>
        </v-tab-item>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_kategori.php'); ?>
            </v-card>
        </v-tab-item>

        <v-tab-item>
            <v-card outlined>
                <?= $this->include('App\Modules\Laporan\Views/laporan_log.php'); ?>
            </v-card>
        </v-tab-item>
    </v-tabs>

    <!-- <v-tabs v-model="tab">
        <v-tabs-slider></v-tabs-slider>

        <v-tab href="#subscribe">
            Subscribe
        </v-tab>

        <v-tab href="#contact">
            Contact
        </v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
        <v-tab-item :key="1" value="subscribe">
            <v-card flat>
                <v-card-text>subscribe</v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item :key="2" value="contact">
            <v-card flat>
                <v-card-text>contact</v-card-text>
            </v-card>
        </v-tab-item>
    </v-tabs-items> -->

</template>

<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
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
        search: "",
        modalShow: false,
        menu: false,
        startDate: "<?= $startDate; ?>",
        endDate: "<?= $endDate; ?>",
        tab: "subscribe",
        dataCash: [],
        dataBank: [],
        dataPenjualan: [],
        dataBarang: [],
        dataStok: [],
        dataKategori: [],
        dataDetailKategori: [],
        dataLabaRugi: [],
        thCash: [{
            text: 'Tanggal',
            value: 'tanggal'
        }, {
            text: 'Faktur',
            value: 'faktur'
        }, {
            text: 'Pemasukan',
            value: 'pemasukan'
        }, {
            text: 'Pengeluaran',
            value: 'pangeluaran'
        }, {
            text: 'Total',
            value: ''
        }, {
            text: 'Keterangan',
            value: 'keterangan'
        }, ],
        thPenjualan: [{
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
            text: 'DPP',
            value: 'subtotal'
        }, {
            text: 'Diskon',
            value: 'diskon'
        }, {
            text: 'Lainnya',
            value: '',
            sortable: false
        }, {
            text: 'Jumlah*',
            value: 'total'
        }, {
            text: 'Laba',
            value: 'total_laba'
        }, {
            text: 'Bayar',
            value: ''
        }, {
            text: 'Kasir',
            value: 'nama'
        }, ],
        thBarang: [{
            text: '#',
            value: 'index'
        }, {
            text: 'Tanggal',
            value: 'created_at'
        }, {
            text: 'Code Item',
            value: 'kode_barang'
        }, {
            text: 'Nama Barang',
            value: 'nama_barang'
        }, {
            text: 'Satuan',
            value: 'satuan'
        }, {
            text: 'Qty',
            value: 'qty'
        }, {
            text: 'Harga',
            value: 'harga_jual'
        }, {
            text: 'Jumlah*',
            value: 'jumlah'
        }, {
            text: '<?= lang('App.tax'); ?>',
            value: 'ppn'
        }, ],
        thBarangStok: [{
            text: '#',
            value: 'index'
        }, {
            text: 'Code Item',
            value: 'kode_barang'
        }, {
            text: 'Barcode',
            value: 'barcode'
        }, {
            text: 'Nama Barang',
            value: 'nama_barang'
        }, {
            text: 'Satuan',
            value: 'satuan'
        }, {
            text: 'Stok',
            value: 'stok'
        }, ],
        thKategori: [{
            text: '#',
            value: 'index'
        }, {
            text: 'Kategori',
            value: 'nama_kategori'
        }, {
            text: 'Jumlah Qty',
            value: 'qty'
        }, {
            text: 'Jumlah Total*',
            value: 'jumlah'
        }],
        idKategori: "",
        namaKategori: "",
        thDetailKategori: [{
            text: '#',
            value: 'faktur'
        }, {
            text: 'Tgl Nota',
            value: 'created_at'
        }, {
            text: 'Diskon Nota',
            value: 'diskon'
        }, {
            text: 'Total Nota',
            value: 'total'
        }, {
            text: 'Nama Barang',
            value: 'nama_barang'
        }, {
            text: 'Jumlah Qty',
            value: 'qty'
        }, {
            text: 'Satuan',
            value: 'satuan'
        }, {
            text: 'Jumlah',
            value: 'jumlah'
        }, {
            text: '<?= lang('App.tax'); ?>',
            value: 'pajak'
        }, {
            text: '<?= lang('App.rounding'); ?>',
            value: 'pembulatan'
        }],
        pemasukanPenjualan: 0,
        pemasukanPenjualanBank: 0,
        pemasukanLain: 0,
        totalPendapatan: 0,
        bebanPokokPendapatan: 0,
        labaKotor: 0,
        pengeluaran: 0,
        pengeluaranBank: 0,
        pengeluaranLain: 0,
        totalPengeluaran: 0,
        labaBersih: 0,
        dataLog: [],
        thLog: [{
            text: '#',
            value: 'id_log'
        }, {
            text: 'Keterangan',
            value: 'keterangan'
        }, {
            text: '<?= lang('App.date'); ?>',
            value: 'created_at'
        }, ],
        dataStokOpname: [],
        thStokOpname: [{
            text: 'Code Item',
            value: 'kode_barang'
        }, {
            text: 'Barcode',
            value: 'barcode'
        }, {
            text: '<?= lang('App.items'); ?>',
            value: 'nama_barang'
        }, {
            text: 'Stok',
            value: 'stok'
        }, {
            text: 'Stok Nyata',
            value: 'stok_nyata'
        }, {
            text: 'Selisih',
            value: 'selisih'
        }, {
            text: 'Nilai',
            value: 'nilai'
        }, {
            text: 'Keterangan',
            value: 'keterangan'
        }, {
            text: '<?= lang('App.date'); ?>',
            value: 'created_at'
        }, ],
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getLaporanCash();
        this.getLaporanPenjualan();
        this.getLaporanBarang();
        this.getLaporanStok();
        this.getLaporanKategori();
        this.getLaporanLabaRugi();
        this.getLaporanLog();
    }

    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
        dataCashWithIndex() {
            return this.dataCash.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataBankWithIndex() {
            return this.dataBank.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataPenjualanWithIndex() {
            return this.dataPenjualan.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataBarangWithIndex() {
            return this.dataBarang.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataStokWithIndex() {
            return this.dataStok.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataKategoriWithIndex() {
            return this.dataKategori.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },

        dataLogWithIndex() {
            return this.dataLog.map(
                (items, index) => ({
                    ...items,
                    index: index + 1
                }))
        },
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        formatMyString(str) {
            if (!str) return '';
            let formattedStr = str.replace(/_/g, ' ');
            formattedStr = formattedStr.charAt(0).toUpperCase() + formattedStr.slice(1);
            return formattedStr;
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

        //Format Ribuan Rupiah versi 2
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
            this.getLaporanCash();
            this.getLaporanPenjualan();
            this.getLaporanBarang();
            this.getLaporanStok();
            this.getLaporanKategori();
            this.getLaporanLabaRugi();
            this.getLaporanLog();
            this.menu = false;
        },

        // Get Kas
        getLaporanCash: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/laporancash?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataCash = data.data;
                        this.menu = false;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataCash = data.data;
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

        // Get Laporan Penjualan
        getLaporanPenjualan: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporanpenjualan?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
                        this.menu = false;
                        //console.log(this.dataBarang);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPenjualan = data.data;
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


        // Get Laporan Barang
        getLaporanBarang: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporanbarang?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        this.menu = false;
                        //console.log(this.dataBarang);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Laporan Stok
        getLaporanStok: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporanstok?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataStok = data.data;
                        this.menu = false;
                        //console.log(this.dataStok);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataStok = data.data;
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

        // Get Laporan Kategori
        getLaporanKategori: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporankategori?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataKategori = data.data;
                        this.menu = false;
                        //console.log(this.dataKategori);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataKategori = data.data;
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

        // Jumlah Total Cash
        sumTotalCash(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.dataCash.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Jumlah Total Bank
        sumTotalBank(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.dataBank.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Jumlah Total Barang
        sumTotalBarang(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.dataBarang.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Jumlah Total Penjualan
        sumTotalPenjualan(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.dataPenjualan.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Get Show Edit
        showItem: function(item) {
            this.modalShow = true;
            this.idKategori = item.id_kategori;
            this.namaKategori = item.nama_kategori;
            this.detailLaporanKategori();
        },

        // Detail Laporan Kategori
        detailLaporanKategori: function() {
            this.loading1 = true;
            axios.get(`<?= base_url(); ?>api/laporandetailkategori?tgl_start=${this.startDate}&tgl_end=${this.endDate}&id_kategori=${this.idKategori}`, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataDetailKategori = data.data;
                        this.menu = false;
                        //console.log(this.dataDetailKategori);
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataDetailKategori = [];
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

        // Get Laporan Laba Rugi
        getLaporanLabaRugi: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporanlabarugi?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataLabaRugi = data.data;
                        this.pemasukanPenjualan = this.dataLabaRugi.pemasukan_penjualan;
                        this.pemasukanLain = this.dataLabaRugi.pemasukan_lain;
                        this.totalPendapatan = this.dataLabaRugi.total_pendapatan;
                        this.bebanPokokPendapatan = this.dataLabaRugi.beban_pokok_pendapatan;
                        this.labaKotor = this.dataLabaRugi.laba_kotor;
                        this.pengeluaran = this.dataLabaRugi.pengeluaran;
                        this.pengeluaranLain = this.dataLabaRugi.pengeluaran_lain;
                        this.totalPengeluaran = this.dataLabaRugi.total_pengeluaran;
                        this.labaBersih = this.dataLabaRugi.laba_bersih;
                        this.menu = false;
                        //console.log(this.dataLabaRugi);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataLabaRugi = data.data;
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

        // Get Laporan Log
        getLaporanLog: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/laporanlog?tgl_start=${this.startDate}&tgl_end=${this.endDate}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataLog = data.data;
                        this.menu = false;
                        //console.log(this.dataLog);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataLog = data.data;
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