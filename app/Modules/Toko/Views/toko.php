<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-3"><?= $title; ?></h1>
    <v-tabs background-color="grey lighten-4" center-active>
        <v-tab>
            <?= lang('App.businessProfile'); ?><br />{{namaToko}}
        </v-tab>
        <v-tab>
            Bayar &amp; Tagihan
        </v-tab>
        <v-tab>
            <?= lang('App.operational'); ?>
        </v-tab>
        <v-tab>
            Printer
        </v-tab>
        <v-tab>
            Reset
        </v-tab>

        <v-tab-item>
            <v-card outlined>
                <v-card-title class="text-h5 font-weight-medium"><?= lang('App.profile'); ?></v-card-title>
                <v-card-text class="elevation-1">
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col cols="12" sm="6" class="mb-n5">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.name'); ?></p>
                                <v-text-field v-model="namaToko" :error-messages="nama_tokoError" outlined></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.name'); ?> Owner</p>
                                <v-text-field v-model="namaPemilik" :error-messages="nama_pemilikError" outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <p class="mb-2 text-subtitle-1"><?= lang('App.address'); ?></p>
                        <v-textarea v-model="alamatToko" :error-messages="alamat_tokoError" rows="2" outlined></v-textarea>

                        <v-row>
                            <v-col cols="12" sm="4">
                                <p class="mb-2 text-subtitle-1">No. Telp</p>
                                <v-text-field v-model="telp" :error-messages="telpError" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="4">
                                <p class="mb-2 text-subtitle-1">E-mail</p>
                                <v-text-field v-model="email" :error-messages="emailError" dense outlined></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="4">
                                <p class="mb-2 text-subtitle-1">NIB</p>
                                <v-text-field v-model="nib" :error-messages="nibError" hint="Nomor Induk Berusaha (Tulis 0 jika tidak ingin ditampilkan)" dense outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <h2 class="mt-3 mb-4 font-weight-medium grey--text text--darken-3"><?= lang('App.numbering'); ?></h2>

                        <v-row>
                            <v-col cols="12" sm="3" class="mb-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codeItem'); ?></p>
                                <v-text-field v-model="kodeBarang" :error-messages="kode_barangError" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3" class="mb-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codeSales'); ?></p>
                                <v-text-field v-model="kodeJual" :error-messages="kode_jualError" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3" class="mb-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codePurchase'); ?></p>
                                <v-text-field v-model="kodeBeli" :error-messages="kode_beliError" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3" class="mb-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codeCash'); ?></p>
                                <v-text-field v-model="kodeKas" :error-messages="kode_kasError" dense outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12" sm="3" class="mt-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codeTax'); ?></p>
                                <v-text-field v-model="kodePajak" :error-messages="kode_pajakError" dense outlined></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="3" class="mt-n4">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.codeCost'); ?></p>
                                <v-text-field v-model="kodeBiaya" :error-messages="kode_biayaError" dense outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <h2 class="mt-3 mb-4 font-weight-medium grey--text text--darken-3">Lainnya (Nilai Default)</h2>
                        <v-row>
                            <v-col cols="12" sm="3">
                                <p class="mb-2 text-subtitle-1"><?= lang('App.discount'); ?> Member %</p>
                                <v-text-field type="number" v-model="diskonMember" :error-messages="diskon_memberError" min="0" dense outlined></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="3">
                                <p class="mb-2 text-subtitle-1">Margin Profit Barang %</p>
                                <v-text-field type="number" v-model="marginBarang" :error-messages="margin_barangError" min="0" hint="Persentase keuntungan default" persistent-hint dense outlined></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>

                    <v-btn large color="primary" @click="update" :loading="loading2" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item>
            <v-card outlined>
                <v-card-title class="text-h5 font-weight-medium">Bayar & Tagihan</v-card-title>
                <v-card-text class="elevation-1">
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col cols="12" sm="6">
                                <p class="mb-0 text-subtitle-1">Gunakan Pembulatan</p>
                                <v-checkbox v-model="pembulatan" true-value="1" false-value="0" :label="`${pembulatan.toString() == '1' ? 'Aktif':'Tidak Aktif'}`"></v-checkbox>

                                <p class="mb-0 text-subtitle-1">Pembulatan Keatas</p>
                                <small>Info! Jika Pembulatan Kebawah maka akan menyebabkan pengurangan pendapatan</small>
                                <v-checkbox v-model="pembulatanKeatas" true-value="1" false-value="0" :label="`${pembulatanKeatas.toString() == '1' ? 'Aktif':'Tidak Aktif'}`" disabled></v-checkbox>

                                <p class="mb-0 text-subtitle-1">Maks. Pembulatan</p>
                                <v-text-field v-model="pembulatanMax" :error-messages="pembulatan_maxError" dense outlined disabled></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6">
                                <p class="mb-0 text-subtitle-1">Include <?= lang('App.tax'); ?>/PPN</p>
                                <v-checkbox v-model="includePpn" true-value="1" false-value="0" :label="`${includePpn.toString() == '1' ? 'Aktif':'Tidak Aktif'}`"></v-checkbox>

                                <p class="mb-2 text-subtitle-1"><?= lang('App.tax'); ?>/PPN %</p>
                                <v-text-field v-model="ppn" :error-messages="ppnError" hint="Masukkan angka tanpa simbol %, Gunakan koma dengan . Contoh: 0.5" dense outlined></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                    <v-btn large color="primary" @click="update" :loading="loading2" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item>
            <v-card outlined>
                <v-card-title class="text-h5 font-weight-medium"><?= lang('App.operationalFeatures'); ?></v-card-title>
                <v-card-text class="elevation-1">
                    <v-list-item>
                        <v-list-item-icon>
                            <v-switch v-model="kodeJualTahun" value="kodeJualTahun" false-value="0" true-value="1" color="primary" @click="setAktifKodeJualTahun"></v-switch>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                <strong>Kode Tahun Pada Nomor Nota</strong><br />
                                Gunakan Kode Tahun Bulan Tanggal pada Nomor Nota (Format dmymd: 250223)
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-switch v-model="scanKeranjang" value="scanKeranjang" false-value="0" true-value="1" color="primary" @click="setAktifScanKeranjang"></v-switch>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                <strong>Scan To Keranjang</strong><br />
                                Gunakan Scan <?= lang('App.items'); ?> langsung masuk Keranjang di Kasir Penjualan (Alternatif Click to Cart)
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-switch v-model="tglJatuhTempo" value="tglJatuhTempo" false-value="0" true-value="1" color="primary" @click="setAktifTglJatuhTempo"></v-switch>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                <strong>Jatuh Tempo Hari atau Tanggal Ditentukan</strong><br />
                                Gunakan tanggal jatuh tempo hari atau tempo tanggal ditentukan
                                <v-row class="mt-3">
                                    <v-col>
                                        <v-text-field v-model="jatuhtempoHari" label="Tempo Hari" :error-messages="jatuhtempo_hariError" suffix="days" dense outlined :disabled="tglJatuhTempo == '1'"></v-text-field>
                                    </v-col>
                                    <v-col>
                                        <v-select v-model="jatuhtempoTanggal" :items="arrayTgl" label="Tempo setiap Tanggal" dense outlined :disabled="tglJatuhTempo == '0'"></v-select>
                                    </v-col>
                                    <v-col>
                                        <v-btn color="primary" @click="update" :loading="loading2" elevation="1">
                                            <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-icon>
                            <v-switch v-model="ketJatuhTempo" value="ketJatuhTempo" false-value="0" true-value="1" color="primary" @click="setAktifKetJatuhTempo"></v-switch>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>
                                <strong>Jatuh Tempo Keterangan: is Required</strong><br />
                                Keterangan Jatuh Tempo pada Sales (POS) harus diisi (Required)
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item>
            <v-card outlined>
                <v-card-title class="text-h5 font-weight-medium">Printer</v-card-title>
                <v-card-text class="elevation-1">
                    <h2 class="font-weight-regular mb-3">Footer Nota</h2>
                    <v-text-field v-model="footerNota" :error-messages="footer_notaError" outlined></v-text-field>

                    <v-row>
                        <v-col>
                            <h2 class="font-weight-regular mb-3">Footer Invoice TTD 1</h2>
                            <quill-editor v-model="footerInvoiceTtd1" ref="quillEditor" :options="editorOption" :error-messages="footer_invoice_ttd1Error"></quill-editor>
                        </v-col>
                        <v-col>
                            <h2 class="font-weight-regular mb-3">Footer Invoice TTD 2</h2>
                            <quill-editor v-model="footerInvoiceTtd2" ref="quillEditor" :options="editorOption" :error-messages="footer_invoice_ttd2Error"></quill-editor>
                        </v-col>
                    </v-row>

                    <br />

                    <h2 class="font-weight-regular mb-3">Footer Keterangan</h2>
                    <quill-editor v-model="footerKeterangan" ref="quillEditor" :options="editorOption" :error-messages="footer_keteranganError" class="mb-5"></quill-editor>

                    <h2 class="mb-3">Printer Thermal</h2>
                    <h2 class="font-weight-regular mb-3">Printer Share Name (.env)</h2>
                    <v-text-field v-model="printerShareName" outlined></v-text-field>

                    <h2 class="font-weight-regular mb-3"><?= lang('App.paperSize'); ?> (Default)</h2>
                    <v-row>
                        <v-col cols="12" sm="9">
                            <v-select v-model="paper" name="papper" :items="paperSize" item-text="label" item-value="value" label="Select" outlined single-line @change=""></v-select>
                        </v-col>
                        <v-col cols="12" sm="3">

                        </v-col>
                    </v-row>

                    <v-btn large color="primary" @click="update" :loading="loading2" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                    </v-btn>

                    <br /><br />

                    <h2 class="font-weight-regular mb-3"><?= lang('App.connection'); ?></h2>
                    <v-row>
                        <v-col>
                            <v-card outlined>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title class="text-h5">
                                            <v-icon large>mdi-usb-port</v-icon> USB Cable Connection (Direct Print)*
                                        </v-list-item-title>
                                        <v-list-item-subtitle class="text-subtitle-1">(*Direct Printing Hanya bisa di Localhost)</v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-icon>
                                        <v-switch v-model="printerUsb" value="printerUsb" false-value="0" true-value="1" color="success" @click="setAktifPrinterUsb" :label="printerUsb == false ? 'Tidak Aktif':'Aktif'"></v-switch>
                                    </v-list-item-icon>
                                </v-list-item>

                                <v-card-actions>
                                    <v-btn color="indigo" text @click="show = !show">
                                        Panduan
                                    </v-btn>

                                    <v-spacer></v-spacer>
                                    <v-btn text link href="<?= base_url('files/Cara_Menggunakan_PrinterThermal_dengan_Mengaktifkan_Printer_Sharing.pdf'); ?>"><v-icon>mdi-file-pdf-box</v-icon> PDF</v-btn>
                                    <v-btn text @click="show = !show">
                                        Lihat <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                                    </v-btn>
                                </v-card-actions>

                                <v-expand-transition>
                                    <div v-show="show">
                                        <v-divider></v-divider>
                                        <v-card-text>
                                            1. Jalankan (Start) Aplikasi Web <?= env('appName'); ?> di <strong>Localhost</strong> (Laragon/Xampp)<br />
                                            2. Pasang Printer Thermal ke PC Komputer dengan menggunakan Kabel USB,<br />
                                            3. Install the driver printer dengan USB printing support (Generic / Text Only driver) sampai muncul nama printernya di Control Panel > Devices and Printers,<br />
                                            4. Buka Windows Control Panel > Devices and Printers > Printernya (Contoh: POS58 Printer) > lalu Klik Kanan pada Printernya > Properties<br />
                                            5. Klik Tab Sharing > Share Name = Receipt Printer<br />
                                            6. Klik OK<br />
                                            7. Turn off password protected sharing pada Windows 10 dan 11. <em>(Go to the Start menu search bar, type in 'control panel,' and select the best match. Select Network and Internet -> Network and Sharing Center. From the left-hand panel, click on Change advanced sharing settings. Expand All Networks tab, select the Turn off password protected sharing and click on Save changes.)</em>
                                        </v-card-text>
                                    </div>
                                </v-expand-transition>
                            </v-card>
                        </v-col>
                        <v-col>
                            <v-card outlined>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title class="text-h5">
                                            <v-icon large>mdi-bluetooth</v-icon> BT Connection (Android/PC Windows)
                                        </v-list-item-title>
                                    </v-list-item-content>
                                    <v-list-item-icon>
                                        <v-switch v-model="printerBT" value="printerBT" false-value="0" true-value="1" color="success" @click="setAktifPrinterBT" :label="printerBT == false ? 'Tidak Aktif':'Aktif'"></v-switch>
                                    </v-list-item-icon>
                                </v-list-item>

                                <v-card-actions>
                                    <v-btn color="indigo" text @click="show1 = !show1">
                                        Panduan
                                    </v-btn>

                                    <v-spacer></v-spacer>
                                    <v-btn text @click="show1 = !show1">
                                        Lihat <v-icon>{{ show1 ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                                    </v-btn>
                                </v-card-actions>

                                <v-expand-transition>
                                    <div v-show="show1">
                                        <v-divider></v-divider>
                                        <v-card-text>
                                            1. Pada Smartphone Android lakukan Pairing Bluetooth dengan Printer Thermalnya terlebih dahulu<br />
                                            2. Install terlebih dahulu di Google Play Store: <a href="https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter" target="_blank">RawBT ESC/POS thermal printer driver</a><br />
                                            3. Buka Aplikasi RawBT, klik Menu <v-icon>mdi-menu</v-icon> > lalu Settings atau icon Gear <v-icon>mdi-cog</v-icon><br />
                                            4. Pilih Connection method: Bluetooth<br />
                                            5. Lalu Connection parameters: Pilih Nama Perangkat Printer Bluetooh milik anda, Contoh: RPP02<br />
                                            6. Lalu Back kembali ke Home<br />
                                            7. Selesai<br />
                                            8. Untuk PC ikuti panduan "RawBT Websocket Server for ESCPOS Printers"
                                        </v-card-text>
                                    </div>
                                </v-expand-transition>
                            </v-card>
                        </v-col>
                    </v-row>
                    <br />
                    <v-card outlined>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="text-h5">
                                    RawBT Websocket Server for ESCPOS Printers
                                </v-list-item-title>
                                <v-list-item-subtitle class="text-subtitle-1">RawBT: Websocket Server for ESC/POS Printers</v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-icon>

                            </v-list-item-icon>
                        </v-list-item>

                        <v-card-actions>
                            <v-btn color="indigo" text @click="show2 = !show2">
                                Panduan
                            </v-btn>

                            <v-spacer></v-spacer>
                            <v-btn text link href="<?= base_url('files/Panduan_RawBT_Websocket_Server_for_ESCPOS_Printers.pdf'); ?>"><v-icon>mdi-file-pdf-box</v-icon> PDF</v-btn>
                            <v-btn text @click="show2 = !show2">
                                Lihat <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                            </v-btn>
                        </v-card-actions>

                        <v-expand-transition>
                            <div v-show="show2">
                                <v-divider></v-divider>
                                <v-card-text>
                                    1. Buka Folder PDF Panduan > Cara Setting Printer Thermal > RawBT Websocket Server for ESCPOS Printers. Copy Folder rawbt_ws_server ke drive C:<br />
                                    2. Buka Folder dist > Edit File server.json<br />
                                    3. Ganti HOME-PC dengan Computer Name di Komputer Windows anda (lihat gambar > Computer-name.png)
                                    <br />
                                    "dest":"smb://HOME-PC/Receipt Printer"
                                    <br />
                                    4. Tambahkan Environment Variables "Path" untuk PHP di Komputer Windows anda, Control Panel > System Properties > Environment variables > buka "Path" tambahkan setelah ; (titik koma)
                                    <br />
                                    Contoh lokasi php (lihat gambar > System Path PHP.png)
                                    <br />
                                    C:\laragon\bin\php\php-7.4.33-Win32-vc15-x64
                                    <br />
                                    atau (lihat gambar > setting_path-win10.jpg)
                                    <br />
                                    C:\xampp\php
                                    <br />
                                    5. Jalankan Laragon atau Xampp Start Apache dan Mysql
                                    <br />
                                    6. Jalankan rawbt.bat
                                </v-card-text>
                            </div>
                        </v-expand-transition>
                    </v-card>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item>
            <v-card outlined>
                <v-card-title class="text-h5 font-weight-medium mb-3">Reset Aplikasi</v-card-title>
                <v-card-subtitle>
                    <h3 class="font-weight-medium mb-3">Melakukan reset ke setelan awal pada aplikasi web ini karena percobaan (trial) sebelum aplikasi siap untuk digunakan.</h3>
                    <strong>Penting:</strong> Beberapa Data dan Transaksi seperti Penjualan, Pembelian, Hutang, Piutang, Cashflow, Bank, Pajak, Biaya, Shift, Stok, Stok Opname, Keranjang, Keranjang Order dan Logs akan Hilang dari Database.<br />
                </v-card-subtitle>
                <v-card-text class="elevation-1">
                    <v-form v-model="valid" ref="form1">
                        <v-btn large color="error" block @click="resetConfirm" class="mb-3"><v-icon>mdi-delete</v-icon> Hapus Data &amp; Transaksi</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-tab-item>
    </v-tabs>
</template>

<!-- Modal Delete Data Transaksi -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalReset" persistent max-width="480px">
            <v-card>
                <v-card-title class="text-h5">
                    <v-icon color="error" class="mr-2" x-large>mdi-alert-octagon</v-icon> Hapus Data Transaksi?
                </v-card-title>
                <v-card-text class="py-5">
                    <h3 class="font-weight-bold">WARNING! <?= lang('App.delConfirm') ?></h3>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="modalReset = false">
                        <?= lang('App.close') ?>
                    </v-btn>
                    <v-btn color="error" text @click="deleteDataTransaksi" :loading="loading5">
                        <strong><?= lang('App.yes') ?> <?= lang('App.delete') ?></strong>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

<!-- Loading -->
<v-dialog v-model="loading" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<!-- End Loading -->
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
        modalEdit: false,
        modalDelete: false,
        modalReset: false,
        tokoData: [],
        idToko: "1",
        namaToko: "",
        nama_tokoError: "",
        alamatToko: "",
        alamat_tokoError: "",
        telp: "",
        telpError: "",
        email: "",
        emailError: "",
        namaPemilik: "",
        nama_pemilikError: "",
        nib: 0,
        nibError: "",
        ppn: 0,
        ppnError: "",
        includePpn: false,
        printerUsb: false,
        printerBT: false,
        kodeJualTahun: false,
        scanKeranjang: false,
        image: null,
        imagePreview: null,
        overlay: false,
        kodeBarang: "",
        kode_barangError: "",
        kodeJual: "",
        kode_jualError: "",
        kodeBeli: "",
        kode_beliError: "",
        kodeKas: "",
        kode_kasError: "",
        kodePajak: "",
        kode_pajakError: "",
        kodeBiaya: "",
        kode_biayaError: "",
        paper: "",
        paperSize: [{
            label: 'Kecil (58mm)',
            value: '58'
        }, ],
        footerNota: "",
        footer_notaError: "",
        footerInvoiceTtd1: "",
        footer_invoice_ttd1Error: "",
        footerInvoiceTtd2: "",
        footer_invoice_ttd2Error: "",
        footerKeterangan: "",
        footer_keteranganError: "",
        printerShareName: "<?= env('printerShareName'); ?>",
        tglJatuhTempo: false,
        jatuhtempoHari: 0,
        jatuhtempo_hariError: "",
        arrayTgl: Array.from(Array(31 + 1).keys()).slice(1),
        jatuhtempoTanggal: 0,
        jatuhtempo_tanggalError: "",
        ketJatuhTempo: false,
        pembulatan: 0,
        pembulatanKeatas: 0,
        pembulatanMax: 100,
        pembulatan_maxError: "",
        diskonMember: 0,
        diskon_memberError: "",
        startDate: "",
        endDate: "",
        marginBarang: 0,
        margin_barangError: ""
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getToko();
        this.getShift();
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        // File Upload
        onFileChange() {
            const reader = new FileReader()
            reader.readAsDataURL(this.image)
            reader.onload = e => {
                this.imagePreview = e.target.result;
                this.uploadFile(this.imagePreview);
            }
        },
        onFileClear() {
            this.image = null;
            this.imagePreview = null;
            this.overlay = false;
            this.snackbar = true;
            this.snackbarMessage = 'Image dihapus';
        },
        uploadFile: function(file) {
            var formData = new FormData() // Split the base64 string in data and contentType
            var block = file.split(";"); // Get the content type of the image
            var contentType = block[0].split(":")[1]; // In this case "image/gif" get the real base64 content of the file
            var realData = block[1].split(",")[1]; // In this case "R0lGODlhPQBEAPeoAJosM...."

            // Convert it to a blob to upload
            var blob = b64toBlob(realData, contentType);
            formData.append('image', blob);
            formData.append('id', this.settingId);
            this.loading2 = true;
            axios.post(`<?= base_url() ?>api/toko/upload`, formData, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.valueEdit = data.data
                        this.overlay = true;
                        this.getToko();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = true;
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

        // Get Toko
        getToko: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/toko', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.tokoData = data.data;
                        this.idToko = this.tokoData.id_toko;
                        this.namaToko = this.tokoData.nama_toko;
                        this.alamatToko = this.tokoData.alamat_toko;
                        this.telp = this.tokoData.telp;
                        this.email = this.tokoData.email;
                        this.namaPemilik = this.tokoData.nama_pemilik;
                        this.nib = this.tokoData.NIB;
                        this.ppn = this.tokoData.PPN;
                        this.includePpn = this.tokoData.include_ppn;
                        this.printerUsb = this.tokoData.printer_usb;
                        this.printerBT = this.tokoData.printer_bluetooth;
                        this.kodeBarang = this.tokoData.kode_barang;
                        this.kodeJual = this.tokoData.kode_jual;
                        this.kodeJualTahun = this.tokoData.kode_jual_tahun;
                        this.kodeBeli = this.tokoData.kode_beli;
                        this.kodeKas = this.tokoData.kode_kas;
                        this.kodePajak = this.tokoData.kode_pajak;
                        this.scanKeranjang = this.tokoData.scan_keranjang;
                        this.kodeBiaya = this.tokoData.kode_biaya;
                        this.paper = this.tokoData.paper_size;
                        this.footerNota = this.tokoData.footer_nota;
                        this.tglJatuhTempo = this.tokoData.jatuhtempo_hari_tanggal;
                        this.jatuhtempoHari = this.tokoData.jatuhtempo_hari;
                        this.jatuhtempoTanggal = parseInt(this.tokoData.jatuhtempo_tanggal);
                        this.ketJatuhTempo = this.tokoData.jatuhtempo_keterangan;
                        this.pembulatan = this.tokoData.pembulatan;
                        this.pembulatanKeatas = this.tokoData.pembulatan_keatas;
                        this.pembulatanMax = this.tokoData.pembulatan_max;
                        this.diskonMember = this.tokoData.diskon_member;
                        this.footerInvoiceTtd1 = this.tokoData.footer_invoice_ttd1;
                        this.footerInvoiceTtd2 = this.tokoData.footer_invoice_ttd2;
                        this.footerKeterangan = this.tokoData.footer_keterangan;
                        this.marginBarang = this.tokoData.margin_barang;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.tokoData = data.data;
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
                    this.loading = false;
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
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/update/${this.idToko}`, {
                    nama_toko: this.namaToko,
                    alamat_toko: this.alamatToko,
                    telp: this.telp,
                    email: this.email,
                    nama_pemilik: this.namaPemilik,
                    nib: this.nib,
                    ppn: this.ppn,
                    include_ppn: this.includePpn,
                    kode_barang: this.kodeBarang,
                    kode_jual: this.kodeJual,
                    kode_beli: this.kodeBeli,
                    kode_kas: this.kodeKas,
                    kode_pajak: this.kodePajak,
                    kode_biaya: this.kodeBiaya,
                    paper_size: this.paper,
                    footer_nota: this.footerNota,
                    jatuhtempo_hari: this.jatuhtempoHari,
                    jatuhtempo_tanggal: this.jatuhtempoTanggal,
                    pembulatan: this.pembulatan,
                    pembulatan_keatas: this.pembulatanKeatas,
                    pembulatan_max: this.pembulatanMax,
                    diskon_member: this.diskonMember,
                    footer_invoice_ttd1: this.footerInvoiceTtd1,
                    footer_invoice_ttd2: this.footerInvoiceTtd2,
                    footer_keterangan: this.footerKeterangan,
                    margin_barang: this.marginBarang
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
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

        // Set Aktif Printer USB
        setAktifPrinterUsb: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktifprinterusb/${this.idToko}`, {
                    printer_usb: this.printerUsb,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Set Aktif Printer Bluetooth
        setAktifPrinterBT: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktifprinterbt/${this.idToko}`, {
                    printer_bluetooth: this.printerBT,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Set Aktif Kode Jual Tahun
        setAktifKodeJualTahun: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktifkodejualtahun/${this.idToko}`, {
                    kode_jual_tahun: this.kodeJualTahun,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Set Aktif Scan Keranjang
        setAktifScanKeranjang: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktifscankeranjang/${this.idToko}`, {
                    scan_keranjang: this.scanKeranjang,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Set Hari Tanggal Jatuh Tempo
        setAktifTglJatuhTempo: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktiftgljatuhtempo/${this.idToko}`, {
                    jatuhtempo_hari_tanggal: this.tglJatuhTempo,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Set Required Keterangan Jatuh Tempo
        setAktifKetJatuhTempo: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/toko/setaktifketjatuhtempo/${this.idToko}`, {
                    jatuhtempo_keterangan: this.ketJatuhTempo,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getToko();
                        this.$refs.form.resetValidation();
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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

        // Modal Confirm Reset
        resetConfirm: function() {
            this.modalReset = true;
        },

        // Reset Data & Transaksi
        deleteDataTransaksi: function() {
            this.loading5 = true;
            axios.post(`<?= base_url(); ?>api/toko/resetdatatransaksi`, {
                    confirm: true
                }, options)
                .then(res => {
                    // handle success
                    this.loading5 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalReset = false;
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
    }
</script>
<?php $this->endSection("js") ?>