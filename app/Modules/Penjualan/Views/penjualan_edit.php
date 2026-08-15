<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <!-- Row -->
    <v-row justify="center">
        <v-dialog v-model="kasir" fullscreen hide-overlay transition="dialog-bottom-transition" scrollable persistent>
            <v-card>
                <v-toolbar color="<?= $navbarColor; ?>" <?= ($navbarColor == 'white' ? 'light' : 'dark'); ?> class="mb-3">
                    <v-btn icon dark link href="<?= base_url('penjualan'); ?>" :disabled="keranjang == '' ? true:false">
                        <v-icon>mdi-arrow-left</v-icon>
                    </v-btn>
                    <v-toolbar-title>
                        <h5 class="font-weight-bold mb-0"><?= $namaToko; ?></h5>
                        <h6 class="font-weight-regular"><?= lang('App.cashier'); ?>: <strong><?= session()->get('nama') ?></strong></h6>
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalInfoOpen" class="mr-1" title="Piutang Customer" alt="Piutang Customer" v-show="dataPiutang != ''"><v-icon color="error">mdi-information-outline</v-icon></v-btn>
                    <v-autocomplete light v-model="id_kontak" label="Customer" :items="dataPelanggan" :item-text="dataPelanggan =>`${dataPelanggan.nama} (${dataPelanggan.telepon}) ${dataPelanggan.grup == 'member' ? 'Member':''}`" item-value="id_kontak" prepend-inner-icon="mdi-account" :loading="loading5" class="mr-4 d-none d-md-flex d-lg-flex d-xl-flex" height="40" style="max-width: 300px !important" auto-select-first solo hide-details @change="changeKontak">
                        <template v-slot:prepend-item>
                            <v-subheader class="mt-n3 mb-n3">{{ dataPelanggan.length }} customer found</v-subheader>
                            <v-list-item ripple @click="modalKontakOpen">
                                <v-icon>mdi-account-plus</v-icon> &nbsp;<?= lang('App.add'); ?> Customer
                            </v-list-item>
                        </template>
                    </v-autocomplete>
                    <v-toolbar-items>
                        <v-btn color="success" class="mr-2" @click="getItemNota" :loading="loading3" title="<?= lang('App.printReceipt'); ?>" :disabled="this.idPenjualan == '' ? true:false">
                            <v-icon>mdi-printer</v-icon> <?= lang('App.receipt') ?><br />
                            {{copyNota == true ? 'Copy':''}}
                        </v-btn>
                        <v-btn color="blue" @click="updatePenjualan" title="<?= lang('App.save'); ?>" class="text-start" :loading="loading1" :disabled="keranjang == '' ? true:false"><v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <!-- Column Kiri -->
                    <v-row>
                        <v-col cols="12" sm="7" md="7">
                            <v-card flat :height="height">
                                <v-autocomplete light v-model="id_kontak" label="Customer" :items="dataPelanggan" :item-text="dataPelanggan =>`${dataPelanggan.nama} (${dataPelanggan.telepon})`" item-value="id_kontak" prepend-inner-icon="mdi-account" :loading="loading5" height="40" class="d-flex d-sm-none" auto-select-first solo @change="changeKontak">
                                    <template v-slot:prepend-item>
                                        <v-subheader class="mt-n3 mb-n3">{{ dataPelanggan.length }} customer found</v-subheader>
                                        <v-list-item ripple @click="modalKontakOpen">
                                            <v-icon>mdi-account-plus</v-icon> &nbsp;<?= lang('App.add'); ?> Customer
                                        </v-list-item>
                                    </template>
                                </v-autocomplete>

                                <?php if ($ppn > 0) { ?>
                                    <v-alert outlined type="error" border="left" dense>
                                        <span class="text-subtitle-2 grey--text text--darken-3">PPN <?= $ppn; ?>% (Status Aktif), Update <v-icon small color="primary">mdi-pencil</v-icon> Item Barang untuk menghitung PPN</span>
                                    </v-alert>
                                <?php } else { ?>
                                    <v-alert outlined type="error" border="left" dense>
                                        <span class="text-subtitle-2 grey--text text--darken-3">PPN <?= $ppn; ?>% (Status Tidak Aktif), Update <v-icon small color="primary">mdi-pencil</v-icon> Item Barang untuk menghitung ulang PPN</span>
                                    </v-alert>
                                <?php } ?>

                                <!-- Start Table Keranjang -->
                                <v-data-table :height="height1" :headers="tbkeranjang" :fixed-header="true" :items="dataKeranjang" :items-per-page="-1" item-key="id_itempenjualan" :loading="loading" loading-text="<?= lang('App.loadingWait'); ?>" style="overflow-y: auto;">
                                    <template v-slot:item="{ item }">
                                        <tr>
                                            <td width="50px">{{item.index}}</td>
                                            <td><strong>{{item.nama_barang}}</strong><br />
                                            <v-chip color="primary" outlined label x-small><strong><?= lang('App.unit'); ?>: {{item.satuan}}</strong></v-chip>
                                            <v-chip color="success" outlined label x-small v-show="item.jumlah_min_grosir > 0"><strong><?= lang('App.wholesale'); ?></strong></v-chip></td>
                                            <td width="180px">
                                                <v-text-field v-model="item.qty" type="number" single-line prepend-icon="mdi-minus" append-outer-icon="mdi-plus" @click:append-outer="increment(item)" @click:prepend="decrement(item)" @input="setJumlah(item)" min="0" style="font-size: 1.125rem;"></v-text-field>
                                            </td>
                                            <td style="font-size: 1rem;">{{RibuanLocaleNoRp(item.jumlah)}}</td>
                                            <td>{{RibuanLocaleNoRp(item.ppn)}}</td>
                                            <td width="50px">
                                                <v-btn color="primary" @click="editItem(item)" icon title="Edit" alt="Edit">
                                                    <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                                <v-btn icon color="error" @click="hapusItem(item)" title="Delete" alt="Delete">
                                                    <v-icon>mdi-delete</v-icon>
                                                </v-btn>
                                            </td>
                                        </tr>
                                    </template>
                                    <template slot="footer.prepend">
                                        <h1 style="font-size: 1.8rem;">{{Ribuan(subtotal + sumTotalPPN('ppn'))}}</h1>&nbsp;<p>Subtotal</p>
                                        <div style="display: none;">{{sumTotalHPP('hpp')}}</div>
                                    </template>
                                </v-data-table>
                                <!-- End Table -->
                                <v-divider></v-divider>
                                <!-- Pembayaran -->
                                <v-card flat width="100%">
                                    <v-row class="mb-n8">
                                        <v-col cols="12" sm="6">
                                            <v-text-field v-model="catatan" label="Catatan atau Keterangan" :error-messages="catatanError" hide-details></v-text-field>
                                        </v-col>
                                        <v-col cols="12" sm="6">
                                            <v-radio-group v-model="metodeBayar" row dense>
                                                <?= lang('App.pay'); ?>: &nbsp;
                                                <v-radio v-for="item in dataBayar" :key="item" :label="`${item.text}`" :value="item.value"></v-radio>
                                            </v-radio-group>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="12" sm="5" <?php if ($cashierpayPos == 'left') { ?>order="first" <?php } else { ?>order="last" <?php } ?>>
                                            <div v-if="metodeBayar == 'cash'">
                                                <v-btn small color="primary" v-on:click="bayar += total" :disabled="bayar == total" elevation="1">{{ total.toLocaleString('id-ID') }}*</v-btn>
                                                <v-btn small v-on:click="bayar = 0" elevation="1">0</v-btn>
                                                <v-btn small v-on:click="bayar += 500" elevation="1">500</v-btn>
                                                <v-btn small v-on:click="bayar += 1000" elevation="1">1k</v-btn>
                                                <v-btn small v-on:click="bayar += 2000" elevation="1">2k</v-btn>
                                                <v-btn small v-on:click="bayar += 5000" elevation="1">5k</v-btn>
                                                <v-btn small v-on:click="bayar += 10000" elevation="1">10k</v-btn>
                                                <v-btn small v-on:click="bayar += 20000" elevation="1">20k</v-btn>
                                                <v-btn small v-on:click="bayar += 50000" elevation="1">50k</v-btn>
                                                <v-btn small v-on:click="bayar += 100000" elevation="1">100k</v-btn>
                                                <v-btn small v-on:click="bayar += 200000" elevation="1">200k</v-btn>
                                                <v-btn small v-on:click="bayar += 500000" elevation="1">500k</v-btn>
                                                <v-btn small v-on:click="bayar += 1000000" elevation="1">1jt</v-btn>
                                            </div>
                                            <div v-else-if="metodeBayar == 'credit'">
                                                <v-form ref="form" v-model="valid">
                                                    <v-menu v-model="date1" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="auto">
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field v-model="jatuhTempo" label="Tgl. Jatuh Tempo" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="jatuh_tempoError" :hint="'Tempo: ' + jatuhTempoHari + ' hari'" persistent-hint></v-text-field>
                                                        </template>
                                                        <v-date-picker v-model="jatuhTempo" @input="date1 = false" color="primary" locale="ID-id"></v-date-picker>
                                                    </v-menu>

                                                    <v-text-field v-model="keterangan" label="Keterangan" :error-messages="keteranganError" single-line hint="Isi keterangan dengan Nama atau lainnya" persistent-hint dense></v-text-field>
                                                </v-form>
                                            </div>
                                        </v-col>
                                        <v-col cols="12" sm="7">
                                            <v-row>
                                                <v-col>
                                                    <v-text-field v-model="diskon" label="Diskon (Rp)" type="number" min="0" outlined dense :rules="[rules.required]" @focus="$event.target.select()" :error-messages="diskonError" :suffix="diskonPersen.toFixed() + '%'"></v-text-field>
                                                    <v-text-field v-model="pajak" label="PPN <?= $ppn; ?>% <?= ($ppnInclude == 1 ? '(Include)' : ''); ?>" type="number" :rules="[rules.decimal]" readonly outlined dense v-show="ppn > 0"></v-text-field>
                                                </v-col>
                                                <v-col>
                                                    <v-text-field v-model="bayar" ref="bayar" label="Bayar (F2)" type="number" min="0" outlined dense @focus="$event.target.select()" v-on:keydown.enter="updatePenjualan" :hint="'*<?= lang('App.rounding'); ?>: ' + pembulatan" persistent-hint></v-text-field>
                                                    <v-text-field v-model="kembali" label="Kembali/Kurang" readonly outlined dense filled></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-card>
                        </v-col>
                        <v-divider inset vertical></v-divider>
                        <!-- Column Kanan -->
                        <v-col cols="12" sm="5" md="5">
                            <?php if ($scanKeranjang == '0') { ?>
                                <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" label="" placeholder="<?= lang('App.searchItem') ?>/Code Item/Barcode/SKU" class="mb-1" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" solo single-line hide-details clearable autofocus :autofocus="'autofocus'"></v-text-field>
                            <?php } else { ?>
                                <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" label="" placeholder="<?= lang('App.searchItem') ?>/Code Item/Barcode/SKU" class="mb-1" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" solo single-line hide-details clearable></v-text-field>
                            <?php } ?>

                            <v-data-table :height="height2" :headers="tbbarang" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :fixed-header="true" :loading="loading2" :search="search" loading-text="<?= lang('App.loadingWait'); ?>" dense>
                                <template v-slot:item="{ item }">
                                    <tr>
                                        <td>
                                            <v-list-item class="ma-n5 pa-n5" two-line>
                                                <v-list-item-avatar size="50" rounded>
                                                    <img v-bind:src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null" />
                                                    <img src="<?= base_url('images/no_image.jpg') ?>" v-else />
                                                </v-list-item-avatar>
                                                <v-list-item-content>
                                                    <p class="text-body-2">{{item.nama_barang}}</p><br />
                                                    <span class="text-caption">
                                                        <?= lang('App.unit'); ?>: {{item.satuan_barang}}/{{item.satuan_nilai}} &bull; <?= lang('App.prc'); ?>:
                                                        <span v-if="item.diskon > 0">
                                                            <span class="text-decoration-line-through">{{ Ribuan(item.harga_jual) }}</span>
                                                            <strong>{{ Ribuan(item.harga_jual - item.diskon) }}</strong>
                                                            <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">-{{item.diskon_persen}}%</v-chip>
                                                        </span>
                                                        <span v-else>{{ Ribuan(item.harga_jual) }}</span>
                                                        &bull; <?= lang('App.stock'); ?>: {{item.stok}}
                                                        &nbsp;
                                                        <v-chip color="success" outlined label x-small v-show="item.jumlah_min_grosir > 0"><strong><?= lang('App.wholesale'); ?></strong></v-chip>
                                                    </span>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </td>
                                        <td width="30%" class="text-caption">{{item.kode_barang}}<br />{{item.barcode}}<br />SKU: {{item.sku ?? "-"}}</td>
                                        <td align="center">
                                            <v-btn small color="primary" @click="saveKeranjang(item)" title="<?= lang('App.addtoCart'); ?>" :disabled="item.stok <= 0">
                                                <v-icon>mdi-cart</v-icon>
                                            </v-btn>
                                        </td>
                                    </tr>
                                </template>
                            </v-data-table>
                            <div class="mt-1">
                                
                            </div>
                            <v-divider></v-divider>
                            <div class="d-flex justify-center mt-3">
                                <v-btn class="text-left mr-2" large @click="modalCalculator = true" elevation="1">Calc<br /> (F8)&nbsp;<v-icon large>mdi-calculator</v-icon></v-btn>
                                <v-select v-model="printer" label="Printer Default" :items="dataPrinter" item-text="text" item-value="value" class="mr-2" @change="saveDefaultPrinter" hide-details></v-select>
                                <!-- Checkbox Auto close -->
                                <v-checkbox v-model="autocloseModalNota" label="Auto Close Nota (5s)" class="mt-2" @change="saveAutoClose"></v-checkbox>
                                <!-- <v-btn class="text-left" large elevation="1" disabled>Cust.<br />Screen &nbsp;<v-icon large>mdi-monitor-account</v-icon>
                                </v-btn> -->
                                <!-- Margin bottom di tampilan mobile -->
                                <div class="d-flex d-sm-none mb-15"></div>
                                <!-- -->
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
            <!-- Tombol Bayar -->
            <v-footer padless fixed>
                <v-btn x-large block class="text-h4 font-weight-medium" color="success" @click="updatePenjualan" title="<?= lang('App.save') ?>" :loading="loading1" :disabled="keranjang == '' ? true:false">
                    <v-icon size="36">mdi-cash-register</v-icon> &nbsp;<?= lang('App.save') ?>: {{ RibuanLocale(sumTotal('jumlah')) }}
                </v-btn>
            </v-footer>
        </v-dialog>
    </v-row>
</template>

<!-- Modal Nota -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalNota" persistent scrollable max-width="600px">
            <v-card class="pa-2">
                <v-card-title class="text-h5">
                    <?= lang('App.receipt') ?> {{copyNota == true ? '(Copy)':''}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalNotaClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" sm="6">
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
                                No: {{fakturNota}}<br />
                                Hr/Tgl: {{dayjs(tanggalNota).format('dddd, DD-MMM-YYYY HH:mm')}}<br />
                                Kasir: <?= session()->get('nama') ?><br />
                                Customer: {{kontakNota}} ({{kontakGrup}})
                            </div>
                            <v-divider></v-divider>
                            <div v-for="item in itemPenjualan" :key="item.id_itempenjualan">
                                {{item.nama_barang}}<br />
                                {{item.qty}} ({{item.satuan}}) x {{RibuanLocaleNoRp(item.harga_jual_grosir == 0 ? item.harga_jual:item.harga_jual_grosir)}}
                                <span class="float-right">{{RibuanLocaleNoRp(item.jumlah)}}</span>
                            </div>
                            <v-divider></v-divider>
                            <div>
                                Subtotal ({{jmlItemNota}} item): <span class="float-right">{{RibuanLocaleNoRp(subtotalNota ?? "0")}}</span><br />
                                PPN {{ppnNota}}%: <span class="float-right">{{RibuanLocaleNoRp(pajakNota ?? "0")}}</span><br />
                                Diskon {{diskonPersenNota}}%: <span class="float-right">{{RibuanLocaleNoRp(diskonNota ?? "0")}}</span><br />
                                <?php if ($pembulatan == 1) : ?>
                                    Pembulatan: <span class="float-right">{{RibuanLocaleNoRp(pembulatanNota ?? "0")}}</span><br />
                                <?php endif; ?>
                                <v-divider></v-divider>
                                <strong>Total: <span class="float-right">{{RibuanLocaleNoRp(totalNota ?? "0")}}</span></strong><br />
                                Bayar: <span class="float-right">{{RibuanLocaleNoRp(bayarNota ?? "0")}}</span><br />
                                <span v-if="kembaliNota >= '0'">
                                    Kembali: <span class="float-right">{{RibuanLocaleNoRp(kembaliNota ?? "0")}}</span><br />
                                </span>
                                <span v-else>
                                    Kurang: <span class="float-right">{{RibuanLocaleNoRp(kembaliNota ?? "0")}}</span><br />
                                </span>
                            </div>
                            <v-divider></v-divider>
                            <div class="mt-2 mb-0 text-center" style="font-size: 11px;line-height: normal;">{{toko.footer_nota}}. Dicetak menggunakan <strong>Aplikasi <?= env('appName') ?></strong> by <?= env('appCompany') ?></div>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <h2><?= lang('App.print'); ?></h2>
                            <v-list dense>
                                <?php if ($cetakUSB == "1") : ?>
                                    <v-list-item @click="printUSB" three-line>
                                        <v-list-item-icon class="me-3">
                                            <v-icon>mdi-usb-port</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Nota (USB*/Thermal)</v-list-item-title>
                                            <v-list-item-subtitle>(*Koneksi USB/Direct Print)</v-list-item-subtitle>
                                            <v-list-item-subtitle>(Khusus Localhost)</v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                                <?php if ($cetakBluetooth == "1") : ?>
                                    <v-list-item @click="printBT" three-line>
                                        <v-list-item-icon class="me-3">
                                            <v-icon>mdi-bluetooth</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Nota (BT*/Thermal)</v-list-item-title>
                                            <v-list-item-subtitle>(*Koneksi BT/Direct Print)</v-list-item-subtitle>
                                            <v-list-item-subtitle>(Install RawBT)</v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                                <v-list-item @click="printHtml" two-line>
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-printer-pos</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Nota (In-Browser/Thermal)</v-list-item-title>
                                        <v-list-item-subtitle>(Printer Thermal/HTML)</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="printInvoiceA4" two-line>
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-printer-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Invoice (In-Browser/A4)</v-list-item-title>
                                        <v-list-item-subtitle>(Printer Std/HTML)</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Nota -->

<!-- Modal Add Kontak -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalKontak" persistent scrollable max-width="900px">
            <v-card>
                <v-card-title><?= lang('App.add') ?> Customer
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalKontakClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form v-model="valid" ref="form">
                        <v-select v-model="tipe" label="Tipe Kontak" :items="dataTipe" item-text="text" item-value="value" :error-messages="tipeError" :loading="loading2" outlined></v-select>

                        <v-text-field v-model="nama" label="Nama" :error-messages="namaError" outlined></v-text-field>

                        <v-text-field v-model="alamat" label="Alamat" :error-messages="alamatError" outlined></v-text-field>

                        <v-text-field type="number" label="Telepon" v-model="telepon" :error-messages="teleponError" hint="Format 62" persistent-hint outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveKontak" :loading="loading5" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Add Kontak -->

<v-dialog v-model="modalInfo" scrollable persistent transition="dialog-top-transition" max-width="500" hide-overlay>
    <template>
        <v-card>
            <v-card-title class="text-h6"><v-icon color="error">mdi-information-outline</v-icon> &nbsp;Piutang Customer</v-card-title>
            <v-card-text class="text-subtitle-1 font-weight-regular py-3">
                Pelanggan <strong>{{namaCustomer}}</strong></span> memiliki {{dataPiutang.length}} Data Piutang yang belum Lunas:
                <ul v-for="(item,i) in dataPiutang" key="id_piutang">
                    <li>Tgl: {{dayjs(item.tanggal).format('DD-MM-YYYY')}}, Jumlah: {{RibuanLocale(item.jumlah_piutang)}}, Sisa: {{RibuanLocale(item.sisa_piutang)}}<br />Ket: {{item.keterangan}} <v-btn small color="dark" link :href="'<?= base_url('piutang'); ?>?faktur=' + item.faktur" target="_blank" title="<?= lang('App.payment'); ?>" alt="<?= lang('App.payment'); ?>" text elevation="0">Tawarkan Bayar&nbsp; <v-icon color="primary">mdi-hand-pointing-right</v-icon></v-btn></li>
                </ul>
                Total: {{RibuanLocale(totalPiutang)}}
            </v-card-text>
            <v-card-actions class="justify-end">
                <v-btn text @click="modalInfoClose"><?= lang('App.close'); ?></v-btn>
            </v-card-actions>
        </v-card>
    </template>
</v-dialog>

<!-- Modal Show Data Barang-->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalShow" scrollable persistent max-width="900px">
            <v-card>
                <v-card-title>
                    <?= lang('App.items'); ?> Barcode "{{scan}}"
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalShowClose" :loading="loading4">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field v-model="searchBarang" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable>
                    </v-text-field>
                    <v-data-table item-key="id_barang" :headers="tbBarang" :items="dataScan" :items-per-page="10" :loading="loading" :search="searchBarang" class="elevation-0" loading-text="<?= lang('App.loadingWait'); ?>" dense>
                        <template v-slot:top>

                        </template>
                        <template v-slot:item="{item}">
                            <tr :class="item.stok <= item.stok_min ? 'red lighten-4':''">
                                <td>{{item.barcode}}</td>
                                <td>{{item.kode_barang}}</td>
                                <td>{{item.nama_barang}}</td>
                                <td>{{item.satuan_barang}}</td>
                                <td>
                                    <v-btn small color="primary" class="py-4" @click="saveKeranjang(item)">
                                        <v-icon>mdi-cart</v-icon> Select
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal -->

<!-- Modal Edit Item Keranjang -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent scrollable width="600px">
            <v-card>
                <v-card-title>Edit Keranjang Barang
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-subtitle>{{namaBarang}}</v-card-subtitle>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-text-field type="number" v-model="hargaJual" label="Harga Jual (Rp)" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateItem" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<!-- Loading4 -->
<v-dialog v-model="loading4" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<!-- End Loading4 -->

<?= $this->include('App\Modules\Partials\Views/calculator'); ?>

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
    }

    // Deklarasi errorKeys
    var errorKeys = []

    // Initial Data
    dataVue = {
        ...dataVue,
        kasir: true,
        scan: "",
        search: "",
        dataBarang: [],
        totalData: 0,
        data: [],
        options: {},
        dataScan: [],
        idBarang: "",
        qty: <?= $data['jumlah']; ?>,
        hpp: <?= $data['hpp']; ?>,
        subtotal: <?= $data['subtotal']; ?>,
        diskon: <?= $data['diskon']; ?>,
        diskonError: "",
        diskonPersen: <?= $data['diskon_persen']; ?>,
        jsubtotal: 0,
        total: <?= $data['total']; ?>,
        bayar: <?= $data['bayar']; ?>,
        kembali: <?= $data['kembali']; ?>,
        toko: [],
        idKeranjang: "",
        keranjang: [],
        itemkeranjang: [],
        idPenjualan: "<?= $data['id_penjualan']; ?>",
        fakturNota: "<?= $data['faktur']; ?>",
        subtotalNota: 0,
        ppnNota: 0,
        pajakNota: 0,
        diskonNota: 0,
        diskonPersenNota: 0,
        totalNota: 0,
        bayarNota: 0,
        kembaliNota: 0,
        tanggalNota: "",
        jmlItemNota: "",
        kontakNota: "",
        kontakGrup: "",
        penjualan: [],
        itemPenjualan: [],
        tbkeranjang: [{
            text: '',
            value: 'id_itempenjualan'
        }, {
            text: '<?= lang('App.items'); ?>',
            value: 'nama_barang'
        }, {
            text: 'Qty',
            value: 'qty',
            sortable: false
        }, {
            text: 'Total',
            value: 'jumlah',
            sortable: false
        }, {
            text: 'PPN',
            value: 'ppn',
            sortable: false
        }, {
            text: '',
            value: 'actions',
            sortable: false
        }, ],
        tbbarang: [{
            text: '<?= lang('App.items'); ?>',
            value: 'nama_barang'
        }, {
            text: 'Code/Barcode',
            value: 'barcode',
            sortable: false
        }, {
            text: '',
            value: 'kode_barang',
            sortable: false
        }, ],
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        modalNota: false,
        modalKontak: false,
        modalInfo: false,
        dataPelanggan: [],
        id_kontak: "<?= $data['id_kontak']; ?>",
        ppn: "<?= $ppn; ?>",
        pajak: <?= $data['pajak']; ?>,
        metodeBayar: "<?= $data['metode_bayar']; ?>",
        dataBayar: [{
            text: '<?= lang('App.payCash'); ?>',
            value: 'cash',
        }, {
            text: '<?= lang('App.credit'); ?>',
            value: 'credit',
        }, ],
        jatuhTempo: "<?= $jatuhTempo; ?>",
        jatuhTempoHari: "<?= $tempoHari; ?>",
        jatuh_tempoError: "",
        keterangan: null,
        keteranganError: "",
        date1: "",
        logo: "<?= base_url() . '/' . $logo; ?>",
        pageCount: 0,
        currentPage: 1,
        limitPage: 10,
        activeColor1: "primary",
        activeColor2: "",
        activeColor3: "",
        dataTipe: [{
            text: 'Pelanggan',
            value: 'Pelanggan'
        }],
        tipe: "Pelanggan",
        tipeError: "",
        nama: "",
        namaError: "",
        alamat: "",
        alamatError: "",
        telepon: "",
        teleponError: "",
        dataPiutang: [],
        namaCustomer: "",
        totalPiutang: "",
        loadingDeleteOpen: false,
        loadingDelete: false,
        searchBarang: "",
        tbBarang: [{
            text: 'Barcode',
            value: 'barcode'
        }, {
            text: 'Code Item',
            value: 'kode_barang'
        }, {
            text: 'Nama Barang',
            value: 'nama_barang'
        }, {
            text: 'Satuan',
            value: 'satuan_barang'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        copyNota: false,
        pembulatan: <?= $data['pembulatan']; ?>,
        pembulatanNota: 0,
        hargaJual: 0,
        namaBarang: "",
        catatan: "<?= $data['catatan']; ?>",
        catatanError: "",
        printer: "",
        dataPrinter: [{
            text: 'Nota USB Thermal',
            value: 'nota_usb_thermal'
        }, {
            text: 'Nota BT Thermal',
            value: 'nota_bt_thermal'
        }, {
            text: 'Nota In-Browser Thermal',
            value: 'nota_inbrowser_thermal'
        }, {
            text: 'Invoice In-Browser A4',
            value: 'invoice_inbrowser_a4'
        }, ],
        autocloseModalNota: false,
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getBarang();
        setTimeout(() => this.getKeranjang(), 1000);
        this.getPelanggan();
        //this.getPenjualan();

        // Keyboard Event
        window.addEventListener('keyup', this.keyMethod);

        // catch autoclose
        const autoclose = localStorage.getItem('autoclose_modalnota');
        if (autoclose == 'true') {
            this.autocloseModalNota = true;
        } else {
            this.autocloseModalNota = false;
        }
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

        height() {
            switch (this.$vuetify.breakpoint.name) {
                case 'sm':
                    return '100vh'
                case 'md':
                    return '100vh'
                case 'lg':
                    return '100vh'
                case 'xl':
                    return '100vh'
            }
        },

        height1() {
            switch (this.$vuetify.breakpoint.name) {
                case 'xs':
                    return '50vh'
                case 'sm':
                    return '35vh'
                case 'md':
                    return '45vh'
                case 'lg':
                    return '50vh'
                case 'xl':
                    return '60vh'
            }
        },

        height2() {
            switch (this.$vuetify.breakpoint.name) {
                case 'xs':
                    return '55vh'
                case 'sm':
                    return '45vh'
                case 'md':
                    return '55vh'
                case 'lg':
                    return '65vh'
                case 'xl':
                    return '70vh'
            }
        },
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

        dataBarang: function() {
            if (this.dataBarang != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        },

        metodeBayar: function() {
            if (this.metodeBayar == 'cash') {
                this.bayar = this.total;
                this.autofocus();
            } else {
                this.bayar = 0;
                this.autofocus();
            }
        },

        bayar: function() {
            this.kembali = parseInt(this.bayar) - parseInt(this.total);
        },

        diskon: function() {
            if (parseInt(this.diskon) == 0) {
                this.diskon = 0;
                this.diskonPersen = 0;
            }

            if (parseInt(this.diskon) > 0) {
                this.bayar = 0;
                let hitung = parseInt(this.jsubtotal) - parseInt(this.diskon)
                let persen = parseInt(this.jsubtotal) - hitung
                this.diskonPersen = (persen / parseInt(this.jsubtotal)) * 100;
            }

            if (parseInt(this.diskon) > parseInt(this.jsubtotal)) {
                this.diskonError = "Diskon terlalu besar";
                //setTimeout(() => this.diskonError = "", 4000);
            } else {
                this.diskonError = "";
            }
        },

        jatuhTempo: function() {
            if (this.jatuhTempo != '') {
                // Here are the two dates to compare
                var date1 = "<?= date('Y-m-d'); ?>";
                var date2 = this.jatuhTempo;

                // First we split the values to arrays date1[0] is the year, [1] the month and [2] the day
                date1 = date1.split('-');
                date2 = date2.split('-');

                // Now we convert the array to a Date object, which has several helpful methods
                date1 = new Date(date1[0], date1[1], date1[2]);
                date2 = new Date(date2[0], date2[1], date2[2]);

                // We use the getTime() method and get the unixtime (in milliseconds, but we want seconds, therefore we divide it through 1000)
                date1_unixtime = parseInt(date1.getTime() / 1000);
                date2_unixtime = parseInt(date2.getTime() / 1000);

                // This is the calculated difference in seconds
                var timeDifference = date2_unixtime - date1_unixtime;

                // in Hours
                var timeDifferenceInHours = timeDifference / 60 / 60;

                // and finaly, in days :)
                var timeDifferenceInDays = timeDifferenceInHours / 24;
                this.jatuhTempoHari = timeDifferenceInDays;
            }
        },

        dataPiutang: function() {
            if (this.dataPiutang != '') {
                this.modalInfo = true;
            } else {
                this.modalInfo = false;
            }
        },

        modalNota: function() {
            if (this.modalNota == true) {
                if (this.printer == 'nota_usb_thermal') {
                    setTimeout(() => this.printUSB(), 2000);
                } else if (this.printer == 'nota_usb_thermal') {
                    setTimeout(() => this.printBT(), 2000);
                } else if (this.printer == 'nota_inbrowser_thermal') {
                    setTimeout(() => this.printHtml(), 2000);
                } else if (this.printer == 'invoice_inbrowser_a4') {
                    setTimeout(() => this.printInvoiceA4(), 2000);
                }

                if (this.autocloseModalNota == true) {
                    if (this.modalNota == true) {
                        setTimeout(() => this.modalNotaClose(), 5000);
                    }
                }
            }
        },
    }

    // Vue Updated
    updatedVue = function() {
        if (localStorage.getItem('printer_default')) {
            this.printer = localStorage.getItem('printer_default');
        }
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        // Save default printer ke local storage saja
        saveDefaultPrinter: function(event) {
            localStorage.setItem('printer_default', this.printer);
        },

        // Save auto close ke local storage saja
        saveAutoClose: function(event) {
            localStorage.setItem('autoclose_modalnota', this.autocloseModalNota);
        },

        // Fungsi Keyboard event
        keyMethod: function(event) {
            console.log('key pressed: ' + event.key + '(' + event.keyCode + ')')
            if (event.key == 'F2' || event.keyCode == 113) {
                this.$refs.bayar.focus();
            } else if (event.key == 'F4' || event.keyCode == 115) {
                this.autofocus();
            } else if (event.key == 'F8' || event.keyCode == 119) {
                this.modalCalculator = true;
            } else if (event.key == 'F9' || event.keyCode == 120) {
                // Jika ada Barang di keranjang
                if (this.keranjang != '') {
                    this.modalDelete = true;
                }
            } else if (event.key == '1' || event.keyCode == 97) {
                this.append('1');
            } else if (event.key == '2' || event.keyCode == 98) {
                this.append('2');
            } else if (event.key == '3' || event.keyCode == 99) {
                this.append('3');
            } else if (event.key == '4' || event.keyCode == 100) {
                this.append('4');
            } else if (event.key == '5' || event.keyCode == 101) {
                this.append('5');
            } else if (event.key == '6' || event.keyCode == 102) {
                this.append('6');
            } else if (event.key == '7' || event.keyCode == 103) {
                this.append('7');
            } else if (event.key == '8' || event.keyCode == 104) {
                this.append('8');
            } else if (event.key == '9' || event.keyCode == 105) {
                this.append('9');
            } else if (event.key == '0' || event.keyCode == 96) {
                this.append('0');
            } else if (event.key == '+' || event.keyCode == 107) {
                this.plus();
            } else if (event.key == '-' || event.keyCode == 109) {
                this.minus();
            } else if (event.key == '*' || event.keyCode == 106) {
                this.times();
            } else if (event.key == '/' || event.keyCode == 111) {
                this.divide();
            } else if (event.key == 'Enter' || event.keyCode == 13) {
                // Jika modal Calculator Tampil
                if (this.modalCalculator == true) {
                    this.equal();
                }
            } else if (event.key == 'c' || event.keyCode == 67) {
                this.clear();
            }
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
            const format = key.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
            return rupiah;
        },

        autofocus() {
            //Autofocus
            let input = document.querySelector('[autofocus]');
            if (input) {
                input.focus()
            }
        },

        // Modal Open
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        // Modal Open
        modalInfoOpen: function() {
            this.modalInfo = true;
            this.autofocus();
        },
        modalInfoClose: function() {
            this.modalInfo = false;
            this.autofocus();
        },

        // Modal Show
        modalShowClose: function() {
            this.modalShow = false;
            this.scan = "";
            this.autofocus();
        },

        // Server-side paginate and sort
        getDataFromApi() {
            this.loading2 = true
            this.fetchData().then(data => {
                this.data = data.items
                this.totalData = data.total
                this.loading2 = false
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

                let items = this.dataBarang
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

        // Handle Search Submit Filter
        handleSubmit: function() {
            this.getBarang();
        },

        // Get Barang
        getBarang: function() {
            this.loading2 = true;
            axios.get(`<?= base_url(); ?>api/barang/kasir`, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        this.data = data.data;
                    }

                    //Autofocus
                    this.autofocus();
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

        // Get Keranjang
        getKeranjang: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/penjualan/item/get/${this.idPenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.keranjang = data.data;
                        const itemkeranjang = this.keranjang.map((row) => (
                            [row.id_barang, row.harga_jual, row.stok, row.qty, row.satuan, row.harga_beli, row.diskon, row.diskon_persen, row.hpp, row.total_laba, row.harga_jual_grosir, row.jumlah_min_grosir]
                        ));
                        this.itemkeranjang = itemkeranjang;
                        //console.log(this.itemkeranjang);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.keranjang = data.data;
                    }

                    //Autofocus
                    this.autofocus();
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
            axios.post(`<?= base_url(); ?>api/penjualan/item/save`, {
                    id_penjualan: this.idPenjualan,
                    id_barang: item.id_barang,
                    harga_jual: item.harga_jual,
                    stok: item.stok,
                    qty: 1,
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.modalShow = false;
                        this.scan = "";
                        this.bayar = 0;
                        this.kembali = 0;
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

        // Delete Item Keranjang
        hapusItem: function(item) {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/penjualan/item/delete/${item.id_itempenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.getBarang();
                        this.bayar = 0;
                        this.kembali = 0;
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

        // Sum Total Pajak
        sumTotalPPN(key) {
            // sum data in give key (property)
            let ppn = 0
            const sum = this.keranjang.reduce((accumulator, currentValue) => {
                return (ppn += +currentValue[key])
            }, 0)
            this.pajak = sum;
            return sum
        },

        // Sum Total
        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.keranjang.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            // subtotal
            this.subtotal = sum;
            // total
            // Periksa dulu toko pembulatan harga
            <?php if ($pembulatan == 1) { ?>
                <?php if ($pembulatan_keatas == 1) { ?>
                    var jsubtotal = Math.ceil((this.subtotal + this.pajak) / 100) * 100;
                <?php } else { ?>
                    var jsubtotal = Math.floor((this.subtotal + this.pajak) / 100) * 100;
                <?php } ?>
                this.pembulatan = jsubtotal - (this.subtotal + this.pajak);
            <?php } else { ?>
                var jsubtotal = this.subtotal + this.pajak;
                this.pembulatan = 0;
            <?php } ?>
            //
            this.jsubtotal = jsubtotal;
            if (this.diskon == 0) {
                // total bayar + jumlah ppn
                this.total = jsubtotal;
                return this.total;
            } else {
                this.total = (jsubtotal) - this.diskon;
                return this.total;
            }
        },

        sumTotalHPP(key) {
            // sum data in give key (property)
            let hpp = 0
            const sum = this.keranjang.reduce((accumulator, currentValue) => {
                return (hpp += +currentValue[key])
            }, 0)
            this.hpp = sum;
            return sum
        },

        increment(item) {
            item.qty++;
            if (item.qty < 0) return;
            this.setJumlah(item);
        },
        decrement(item) {
            item.qty--;
            if (item.qty < 0) {
                item.qty = 0;
            } else {
                this.setJumlah(item);
            };
        },

        // Set Jumlah Item
        setJumlah: function(item) {
            this.loading = true;
            this.idKeranjang = item.id_itempenjualan;
            this.qty = item.qty;
            this.idBarang = item.id_barang;
            this.hargaJual = item.harga_jual;
            axios.put(`<?= base_url(); ?>api/penjualan/item/update/${this.idKeranjang}`, {
                    id_barang: this.idBarang,
                    qty: this.qty,
                    harga_jual: this.hargaJual
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.getKeranjang();
                        this.bayar = 0;
                        this.kembali = 0;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Update Penjualan
        updatePenjualan: function(item) {
            this.loading1 = true;
            const data = this.itemkeranjang;
            if (this.metodeBayar == 'cash') {
                var metode = 'cash';
            } else if (this.metodeBayar == 'credit') {
                var metode = 'credit';
            } 
            //console.log(data);
            axios.put(`<?= base_url(); ?>api/penjualan/update/${this.idPenjualan}/${metode}`, {
                    data: data,
                    bayar: this.bayar,
                    hpp: this.hpp,
                    subtotal: this.subtotal,
                    diskon: this.diskon,
                    diskon_persen: this.diskonPersen,
                    total: this.total,
                    kembali: this.kembali,
                    id_kontak: this.id_kontak,
                    ppn: this.ppn,
                    pajak: this.pajak,
                    metode_bayar: this.metodeBayar,
                    jatuh_tempo: this.jatuhTempo,
                    keterangan: this.keterangan,
                    pembulatan: this.pembulatan,
                    catatan: this.catatan,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.copyNota = false;
                        //Buat refresh Barang
                        setTimeout(() => this.getBarang(), 1000);
                        setTimeout(() => this.getKeranjang(), 1000);
                        //Buat tampil Nota
                        setTimeout(() => this.getItemNota(), 2000);
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

        //Get Toko
        getToko: function() {
            axios.get(`<?= base_url(); ?>api/toko`, options)
                .then(res => {
                    // handle success
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.toko = data.data;
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

        //Get Nota
        getPenjualan: function() {
            this.loading3 = true;
            axios.get(`<?= base_url(); ?>api/penjualan/${this.idPenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.penjualan = data.data;
                        this.fakturNota = this.penjualan.faktur;
                        this.subtotalNota = this.penjualan.subtotal;
                        this.ppnNota = this.penjualan.PPN;
                        this.pajakNota = this.penjualan.pajak;
                        this.diskonNota = this.penjualan.diskon;
                        this.diskonPersenNota = this.penjualan.diskon_persen;
                        this.totalNota = this.penjualan.total;
                        this.bayarNota = this.penjualan.bayar;
                        this.kembaliNota = this.penjualan.kembali;
                        this.tanggalNota = this.penjualan.created_at;
                        this.jmlItemNota = this.penjualan.jumlah;
                        this.kontakNota = this.penjualan.nama_kontak;
                        this.kontakGrup = this.penjualan.grup;
                        this.pembulatanNota = this.penjualan.pembulatan;
                        this.getToko();
                        //console.log(this.penjualan);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.penjualan = data.data;
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

        //Close Modal Nota
        modalNotaClose: function() {
            this.modalNota = false;
            this.copyNota = true;
        },

        //Get Item Nota
        getItemNota: function() {
            this.loading3 = true;
            axios.get(`<?= base_url(); ?>api/cetaknota/${this.idPenjualan}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.itemPenjualan = data.data;
                        this.modalNota = true;
                        this.getPenjualan();
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

        // Print
        printUSB: function() {
            this.loading4 = true;
            //console.log(data);
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
        printBT: function() {
            this.loading4 = true;
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

        // Get Pelanggan
        getPelanggan: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/kontak/pelanggan', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPelanggan = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPelanggan = data.data;
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

        // Modal Open
        modalKontakOpen: function() {
            this.modalKontak = true;
        },
        modalKontakClose: function() {
            this.modalKontak = false;
            this.$refs.form.resetValidation();
            this.getKeranjang();
        },

        // Save Kontak
        saveKontak: function() {
            this.loading5 = true;
            axios.post('<?= base_url(); ?>api/kontak/save', {
                    tipe: this.tipe,
                    nama: this.nama,
                    perusahaan: null,
                    alamat: this.alamat,
                    telepon: this.telepon,
                    email: this.nama + "@gmail.com",
                    nikktp: null,
                    npwp: null,
                }, options)
                .then(res => {
                    // handle success
                    this.loading5 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getPelanggan();
                        this.id_kontak = data.lastID;
                        this.tipe = "";
                        this.nama = "";
                        this.alamat = "";
                        this.telepon = "";
                        this.modalKontak = false;
                        this.$refs.form.resetValidation();
                        this.autofocus();
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
                        this.modalKontak = true;
                        this.$refs.form.validate();
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading5 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        changeKontak: function() {
            this.getPiutang();
            this.autofocus();
        },

        // Get Piutang
        getPiutang: function() {
            this.loading5 = true;
            axios.get(`<?= base_url(); ?>api/find_piutang/${this.id_kontak}`, options)
                .then(res => {
                    // handle success
                    this.loading5 = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataPiutang = data.data;
                        this.namaCustomer = data.nama_kontak;
                        this.totalPiutang = data.total_piutang;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataPiutang = [];
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading5 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Get Item Edit Keranjang
        editItem: function(item) {
            this.modalEdit = true;
            this.idKeranjang = item.id_itempenjualan;
            this.hargaJual = item.harga_jual;
            this.qty = item.qty;
            this.idBarang = item.id_barang;
            this.namaBarang = item.nama_barang;
        },

        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        // Update Item Keranjang
        updateItem: function() {
            this.loading = true;
            axios.put(`<?= base_url() ?>api/penjualan/item/update/${this.idKeranjang}`, {
                    id_barang: this.idBarang,
                    qty: this.qty,
                    harga_jual: this.hargaJual
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = false;
                        this.getKeranjang();
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

        printHtml: function() {
            window.open('<?= base_url('penjualan/printnota-html?id_penjualan='); ?>' + this.idPenjualan, "_blank", "popup=yes");
        },

        printInvoiceA4: function() {
            window.open('<?= base_url('penjualan/print-invoice-a4?id_penjualan='); ?>' + this.idPenjualan, "_blank", "popup=yes");
        },
    }
</script>
<?php $this->endSection("js") ?>