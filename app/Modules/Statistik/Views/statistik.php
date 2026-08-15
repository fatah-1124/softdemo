<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium"><?= $title; ?> &nbsp;<span class="font-weight-regular"><?= date('d-m-Y', strtotime($cari)); ?></span>
        <template>
            <v-menu v-model="menu" :close-on-content-click="false" offset-y>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-calendar-filter</v-icon>
                    </v-btn>
                </template>
                <v-card width="200">
                    <? //= form_open("statistik?cari=$cari"); 
                    ?>
                    <?= form_open(); ?>
                    <v-card-text>
                        <p class="mb-1">Select Date:</p>
                        <v-text-field type="date" id="cari" class="form-control" name="cari" value="<?= $cari; ?>"></v-text-field>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn text @click="menu = false">
                            <?= lang('App.close'); ?>
                        </v-btn>
                        <button class="v-btn v-btn--text theme--light elevation-0 v-size--default primary--text">Filter</button>
                    </v-card-actions>
                    <?= form_close(); ?>
                </v-card>
            </v-menu>
        </template>
    </h1>
    <p><?= lang('App.statDesc'); ?>.</p>

    <v-row class="mb-3">
        <v-col md="6" cols="sm" class="pb-2">
            <v-card>
                <v-list-item two-line class="mb-n4">
                    <v-list-item-content>
                        <v-list-item-title class="text-h6 font-weight-medium">
                            <?= lang('App.salesSummary'); ?> <?= date('d/m/Y', strtotime($cari)); ?>
                        </v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-avatar><v-icon x-large color="primary" class="float-right">mdi-cash-register</v-icon></v-list-item-avatar>
                </v-list-item>

                <v-list-item class="mb-n4">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.income'); ?></v-list-item-title>
                        <h1 class="mb-3">{{Ribuan(<?= ($totalTrxHariini - $sisaPiutangHariini) ?? "0"; ?>)}}*</h1>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.items'); ?> Terjual</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong><?= $sumQtyHariini ?? "0"; ?></strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.sales'); ?> <?= lang('App.today'); ?></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong><?= $countTrxHariini; ?></strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title>Total <?= lang('App.income'); ?>*</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong>{{RibuanLocale(<?= ($totalTrxHariini - $sisaPiutangHariini) ?? "0"; ?>)}}</strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title>Total Laba</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong>{{RibuanLocale(<?= ($sumLabaHariini) ?? "0"; ?>)}}</strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.yesterday'); ?></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong><?= $countTrxHarikemarin; ?></strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.income'); ?> <?= lang('App.yesterday'); ?>*</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong>{{RibuanLocale(<?= ($totalTrxHarikemarin - $sisaPiutangHarikemarin) ?? "0"; ?>)}}</strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title>Laba <?= lang('App.yesterday'); ?></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong>{{RibuanLocale(<?= ($sumLabaHarikemarin) ?? "0"; ?>)}}</strong></v-chip>
                    </v-list-item-action>
                </v-list-item>

                <v-list-item class="mb-n3">
                    <v-list-item-content>
                        <v-list-item-title><?= lang('App.expense'); ?> <?= lang('App.yesterday'); ?>*</v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-chip><strong>-{{RibuanLocale(<?= $kasKeluarHarikemarin ?? "0"; ?>)}}</strong></v-chip>
                    </v-list-item-action>
                </v-list-item>
            </v-card>
        </v-col>

        <v-col md="6" cols="sm" class="pb-2">
            <div>
                <h3 class="mb-2"><?= lang('App.debts'); ?></h3>
                <h4 class="font-weight-regular">Belum Dibayar: <strong>{{RibuanLocale(<?= $sisaHutang ?? "0"; ?>)}}</strong></h4>
                <h4 class="font-weight-regular">Akan Jatuh Tempo: <strong><?= $hutangAkanTempo ?? "0"; ?></strong></h4>
                <h4 class="font-weight-regular">Jatuh Tempo Hari ini: <strong><?= $hutangTempoHariini ?? "0"; ?></strong></h4>
                <h4 class="font-weight-regular">Lewat Jatuh Tempo: <strong><?= $hutangLewatTempo ?? "0"; ?></strong></h4>
            </div>
            <v-divider class="my-3"></v-divider>
            <div>
                <h3 class="mb-2"><?= lang('App.receivables'); ?></h3>
                <h4 class="font-weight-regular">Belum Dibayar: <strong>{{RibuanLocale(<?= $sisaPiutang ?? "0"; ?>)}}</strong></h4>
                <h4 class="font-weight-regular">Akan Jatuh Tempo: <strong><?= $piutangAkanTempo ?? "0"; ?></strong></h4>
                <h4 class="font-weight-regular">Jatuh Tempo Hari ini: <strong><?= $piutangTempoHariini ?? "0"; ?></strong></h4>
                <h4 class="font-weight-regular">Lewat Jatuh Tempo: <strong><?= $piutangLewatTempo ?? "0"; ?></strong></h4>
            </div>
            <v-divider class="my-3"></v-divider>
            <div>
                <h3 class="mb-2"><?= lang('App.mostSoldItems'); ?></h3>
                <?php if ($barangTerlaris != null) { ?>
                    <ol>
                        <?php foreach ($barangTerlaris as $row) { ?>
                            <li><strong><?= $row['nama_barang']; ?></strong> (<?= $row['satuan_nilai']; ?> <?= $row['satuan_barang']; ?>) Total: <?= $row['qty']; ?></li>
                        <?php } ?>
                    </ol>
                <?php } else { ?>
                    <br />
                    <v-alert type="error" text>
                        <span class="grey--text text--darken-3"><?= lang('App.noItemsSold'); ?></span>
                    </v-alert>
                    <br />
                <?php } ?>
            </div>
        </v-col>
    </v-row>
</template>

<template>
    <v-row class="mb-3">
        <v-col cols="12" md="12">
            <v-card>
                <v-card-title><?= lang('App.dailyTrx'); ?></v-card-title>
                <v-card-subtitle><?= $cari; ?></v-card-subtitle>
                <v-card-text>
                    <bar-chart1></bar-chart1>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="12">
            <v-card>
                <v-card-title><?= lang('App.dailyIncome'); ?>*</v-card-title>
                <v-card-subtitle><?= monthyear_indo(date('Y-m', strtotime($cari))); ?></v-card-subtitle>
                <v-card-text>
                    <bar-chart2></bar-chart2>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<template>
    <v-card class="mx-auto text-center mb-3">
        <v-card-title>
            <?= lang('App.sales') ?> <?= date('Y', strtotime($cari)); ?>
        </v-card-title>
        <v-sparkline :labels="sparklineLabel" :value="sparklineData" padding="18" label-size="4" color="grey" :gradient="['#007bff','cyan']" :line-width="2" :stroke-linecap="'round'">
        </v-sparkline>
    </v-card>
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
        menu: false,
        barang: [],
        sparklineLabel: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        sparklineData: JSON.parse("<?= json_encode($transaksi) ?>"),
        pageCount: 0,
        currentPage: 1,
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        //this.getProducts();

        Vue.component('bar-chart1', {
            extends: VueChartJs.Bar,
            mounted() {
                this.renderChart({
                    labels: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '00'],
                    datasets: [{
                        data: JSON.parse('<?= json_encode($harian) ?>'),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                    }]
                }, {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        xAxes: [{
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            },
                            ticks: {
                                maxTicksLimit: 24
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + '<?= lang('App.transaction'); ?>: ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                })
            }

        })

        Vue.component('bar-chart2', {
            extends: VueChartJs.Bar,
            mounted() {
                this.renderChart({
                    labels: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                    datasets: [{
                        data: JSON.parse('<?= json_encode($pemasukan) ?>'),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(255, 205, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                    }]
                }, {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        xAxes: [{
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            },
                            ticks: {
                                maxTicksLimit: 31
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: true,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + 'Rp. ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                })
            }
        })

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

        // Get Product
        getProducts: function() {
            this.show = true;
            axios.get(`<?= base_url(); ?>api/barang/terbaru?page=${this.currentPage}`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        this.barang = data.data;
                        this.pageCount = Math.ceil(data.total_page / data.per_page);
                        this.show = false;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Pagination
        handlePagination: function(pageNumber) {
            this.show = true;
            axios.get(`<?= base_url(); ?>api/barang/terbaru?page=${pageNumber}`, options)
                .then((res) => {
                    var data = res.data;
                    this.barang = data.data;
                    this.pageCount = Math.ceil(data.total_page / data.per_page);
                    this.show = false;
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