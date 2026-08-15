<!-- Pendapatan -->
<v-card>
    <v-card-title><?= lang('App.income'); ?>
        <v-spacer></v-spacer>
        {{dayjs(startDate).format('DD-MM-YYYY')}} &mdash; {{dayjs(endDate).format('DD-MM-YYYY')}}
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text class="pt-0">
        <v-list class="pt-0">
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="font-weight-medium"><?= lang('App.sales'); ?></v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item class="ms-7">
                <v-list-item-content>
                    <v-list-item-title>Pemasukan <?= lang('App.sales'); ?> (KAS)</v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{Ribuan(pemasukanPenjualan ?? "0")}}</h3>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item class="ms-7">
                <v-list-item-content>
                    <v-list-item-title>Penghasilan Lain (Pemasukan Lainnya &amp; Mutasi)</v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{Ribuan(pemasukanLain ?? "0")}}</h3>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="font-weight-medium">Total <?= lang('App.income'); ?></v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{Ribuan(totalPendapatan ?? "0")}}</h3>
            </v-list-item>
        </v-list>
    </v-card-text>
</v-card>

<v-card>
    <v-card-title>Beban Pokok Penjualan</v-card-title>
    <v-divider></v-divider>
    <v-card-text class="pt-0">
        <v-list class="pt-0">
            <v-list-item class="ms-7">
                <v-list-item-content>
                    <v-list-item-title>Beban Pokok Pendapatan (HPP)</v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{Ribuan(bebanPokokPendapatan ?? "0")}}</h3>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="font-weight-medium">Total Beban Pokok Penjualan</v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{Ribuan(bebanPokokPendapatan ?? "0")}}</h3>
            </v-list-item>
        </v-list>
    </v-card-text>
</v-card>
<v-card>
    <v-card-title><?= lang('App.grossProfit'); ?>
        <v-spacer></v-spacer>
        {{RibuanLocale(labaKotor) ?? "0"}}
    </v-card-title>
</v-card>

<!-- Pengeluaran -->
<v-card>
    <v-card-title><?= lang('App.operatingCosts'); ?></v-card-title>
    <v-divider></v-divider>
    <v-card-text class="pt-0">
        <v-list class="pt-0">
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="font-weight-medium"><?= lang('App.operatingCosts'); ?></v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item class="ms-7">
                <v-list-item-content>
                    <v-list-item-title>Pengeluaran &amp; Biaya KAS<br /><small>(Termasuk Potongan Pembulatan Kebawah)</small></v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{RibuanLocale(pengeluaran ?? "0")}}</h3>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item class="ms-7">
                <v-list-item-content>
                    <v-list-item-title>Pengeluaran Lain</v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{RibuanLocale(pengeluaranLain ?? "0")}}</h3>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="font-weight-medium">Total <?= lang('App.operatingCosts'); ?></v-list-item-title>
                </v-list-item-content>
                <v-spacer></v-spacer>
                <h3 class="font-weight-regular">{{RibuanLocale(totalPengeluaran ?? "0")}}</h3>
            </v-list-item>
        </v-list>
    </v-card-text>
</v-card>

<v-card>
    <v-card-title><?= lang('App.netProfit'); ?>
        <v-spacer></v-spacer>
        {{RibuanLocale(labaBersih) ?? "0"}}
    </v-card-title>
</v-card>

<v-card>
    <v-card-title>
        <v-btn outlined :href="'<?= base_url('laporan/labarugi-pdf') ?>' + '?tgl_start=' + startDate + '&tgl_end=' + endDate" target="_blank">
            <v-icon>mdi-download</v-icon> PDF
        </v-btn>
    </v-card-title>
</v-card>