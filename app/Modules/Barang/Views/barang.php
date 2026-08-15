<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2">
        <v-row>
            <v-col cols="12" sm="5" class="mb-1">
                <?= $title; ?>&nbsp;
                <v-btn color="primary" large href="<?= base_url('barang/baru') ?>" title="<?= lang('App.add') . ' ' . $title; ?>" alt="<?= lang('App.add') . ' ' . $title; ?>" elevation="1">
                    <v-icon>mdi-plus</v-icon> <?= lang('App.add'); ?>
                </v-btn>
            </v-col>
            <v-col cols="12" sm="7">
                <div class="float-end">
                    <v-btn small outlined color="success" class="py-4" @click="excelMultiple(selected)" :disabled="selected == ''" :loading="loading4" title="Export Data" alt="Export Data" elevation="1">
                        <v-icon>mdi-download</v-icon> Export <span v-show="selected != ''">({{this.selected.length}})
                    </v-btn>
                    <v-btn small outlined @click="barcodeMultiple(selected)" :disabled="selected == ''" :loading="loading6" class="py-4" title="Print Barcode" alt="Print Barcode" elevation="1">
                        <v-icon>mdi-barcode</v-icon> Barcode <span v-show="selected != ''">({{this.selected.length}})</span>
                    </v-btn>
                    <v-btn small outlined @click="labelMultiple(selected)" :disabled="selected == ''" :loading="loading5" class="py-4" title="Print Label Rack" alt="Print Label Rack" elevation="1">
                        <v-icon>mdi-tag-text-outline</v-icon> Label <span v-show="selected != ''">({{this.selected.length}})</span>
                    </v-btn>
                    <v-btn small outlined color="error" @click="confirmDelete(selected)" :disabled="selected == ''" class="py-4" title="Delete Multiple" alt="Delete Multiple" elevation="1">
                        <v-icon>mdi-delete</v-icon> <?= lang('App.delete') ?> <span v-show="selected != ''">({{this.selected.length}})
                    </v-btn>
                    <v-btn small outlined color="success" class="py-4" href="<?= base_url('excel/import') ?>" title="Import Data" alt="Import Data" elevation="1">
                        <v-icon>mdi-file-excel-box</v-icon> Import
                    </v-btn>
                </div>
            </v-col>
        </v-row>
    </h1>
    <v-card>
        <v-card-title>
            <v-row>
                <v-col cols="12" sm="6">
                    <v-autocomplete v-model="kategori" label="Filter Kategori" :items="dataKategori" item-text="nama_kategori" item-value="id_kategori" class="mr-2" multiple chips hide-details clearable prepend-icon="mdi-filter" append-outer-icon="mdi-plus-thick" @click:append-outer="addKategori" @change="handleKategori" @click:clear="handleClear"></v-autocomplete>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-autocomplete v-model="satuan" label="Data Satuan" :items="dataSatuan" item-text="nama_satuan" item-value="id_satuan" class="mr-2" hide-details chips clearable append-outer-icon="mdi-plus-thick" @click:append-outer="addSatuan"></v-autocomplete>
                </v-col>
            </v-row>
        </v-card-title>
        <v-card-text class="overflow-auto">
            <v-btn-toggle v-model="tabsBarang" :color="tabColor" mandatory borderless group>
                <v-btn value="semua-barang" elevation="2" class="pa-2"><v-icon v-show="tabsBarang == 'semua-barang'">mdi-checkbox-marked-outline</v-icon>&nbsp;<?= lang('App.allItems'); ?> ({{ jmlSemuaBarang }})</v-btn>
                <v-btn value="stok-habis" elevation="2" class="pa-2"><v-icon v-show="tabsBarang == 'stok-habis'">mdi-checkbox-marked-outline</v-icon>&nbsp;<?= lang('App.outofStock'); ?> ({{ jmlStokHabis }})</v-btn>
                <v-btn value="non-aktif" elevation="2" class="pa-2"><v-icon v-show="tabsBarang == 'non-aktif'">mdi-checkbox-marked-outline</v-icon>&nbsp;<?= lang('App.nonActive'); ?> ({{ jmlNonaktif }})</v-btn>
            </v-btn-toggle>

            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable class="mb-0 float-end col-sm-12 col-md-5 col-lg-6"></v-text-field>

            <?php if (session()->getFlashdata('error')) { ?>
                <v-alert type="error" dismissible v-model="alert" dense class="mt-3">
                    <?= session()->getFlashdata('error') ?>
                </v-alert>
            <?php } ?>
        </v-card-text>

        <!-- Start Table -->
        <v-data-table v-model="selected" item-key="id_barang" show-select :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>">
            <template v-slot:top>

            </template>
            <template v-slot:item="{ item, isSelected, select}">
                <tr :class="isSelected ? 'grey lighten-2':'' || parseInt(item.stok) <=  parseInt(item.stok_min) ? 'red lighten-4':''" @click="toggle(isSelected,select,$event)">
                    <td>
                        <v-icon color="primary" v-if="isSelected">mdi-checkbox-marked</v-icon>
                        <v-icon v-else>mdi-checkbox-blank-outline</v-icon>
                    </td>
                    <td width="50">
                        <v-menu right bottom min-width="200px">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn icon v-bind="attrs" v-on="on">
                                    <v-icon>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list dense>
                                <v-list-item @click="editItem(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-pencil-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Edit</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="openLabel(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-tag-text-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Label</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="openBarcode(item)" :disabled="item.barcode == '' || item.barcode == null">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-barcode</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Barcode</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="deleteItem(item)">
                                    <v-list-item-icon class="me-3">
                                        <v-icon>mdi-delete-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>Hapus</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </td>
                    <td style="max-width:320px">
                        <a link @click="editItem(item)">
                            <v-list-item class="ma-n3 pa-n3" two-line>
                                <v-list-item-avatar size="50" rounded>
                                    <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" :src="'<?= base_url() ?>' + item.media_path" v-if="item.media_path != null"></v-img>
                                    <v-img lazy-src="<?= base_url('images/no_image.jpg') ?>" src="<?= base_url('images/no_image.jpg') ?>" v-else></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <p class="text-subtitle-2 text-underlined primary--text">{{item.nama_barang}}</p>
                                    <p class="mb-0">{{item.kode_barang}}</p>
                                    <p class="mb-0">SKU: {{item.sku ?? "-"}}</p>
                                    <p class="mb-0"><v-icon small>mdi-tag-outline</v-icon> <?= lang('App.category'); ?>: {{item.nama_kategori}}</p>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <v-chip color="success" outlined label x-small v-show="item.jumlah_min_grosir > 0"><strong><?= lang('App.wholesale'); ?></strong></v-chip>
                                </v-list-item-action>
                            </v-list-item>

                        </a>
                    </td>
                    <td>{{item.barcode}}</td>
                    <td>{{item.satuan_barang}}/{{item.satuan_nilai}}</td>
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
                                <v-chip color="red" label x-small dark class="px-1" title="<?= lang('App.discount'); ?>">-{{item.diskon_persen}}%</v-chip><br />{{ Ribuan(item.harga_jual - item.diskon) }}
                            </div>
                            <div v-else>{{ Ribuan(item.harga_jual) }}</div>
                            <template v-slot:input>
                                <v-text-field v-model="item.harga_jual" type="number" class="pt-3" append-icon="mdi-content-save" @click:append="setHargaJual(item)" outlined dense hide-details single-line :disabled="item.diskon > 0"></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </td>
                    <td>
                        <v-edit-dialog v-model="editStok" large persistent :return-value.sync="item.stok" @save="setStok(item)" @cancel="" @open="" @close="handleSubmit" cancel-text="<?= lang('App.close'); ?>" save-text="<?= lang('App.save'); ?>">
                            <strong>{{item.stok}}</strong><br />
                            <?= lang('App.warehouse'); ?>: {{item.stok_gudang}}
                            <template v-slot:input>
                                <v-text-field v-model="item.stok" type="number" label="<?= lang('App.stock'); ?> <?= lang('App.active'); ?>" class="pt-3" min="0" outlined hide-details></v-text-field>
                                <v-text-field v-model="item.stok_gudang" label="<?= lang('App.stock') . ' ' . lang('App.warehouse'); ?>" @click="transferStok(item)" append-icon="mdi-swap-horizontal-bold" class="pt-3" min="0" outlined hide-details></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </td>
                    <td>
                        <v-switch v-model="item.active" value="active" false-value="0" true-value="1" color="success" @click="setAktif(item)"></v-switch>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table -->
    </v-card>
</template>

<!-- Modal -->

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
                        <h2 class="font-weight-regular"><?= lang('App.delConfirm') ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalDelete = false" elevation="1"><?= lang('App.no') ?></v-btn>
                    <v-btn color="error" dark large @click="deleteBarang" :loading="loading" elevation="1"><?= lang('App.yes') ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<template>
    <v-row justify="center">
        <v-dialog v-model="modalDeleteMultiple" persistent max-width="600px">
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
                    <v-btn @click="modalDeleteMultiple = false" elevation="1" large><?= lang('App.close'); ?></v-btn>
                    <v-btn color="red" dark @click="deleteMultiple" :loading="loading" elevation="1" large><?= lang('App.delete'); ?> (All)</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

<!-- Modal Barcode -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalBarcode" persistent max-width="300">
            <v-card>
                <v-card-title class="text-h6 mb-3">
                    Jumlah
                </v-card-title>
                <v-card-text>
                    <v-text-field type="number" v-model="jmlBarcode" label="Jumlah" hide-details outlined></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="closeBarcode">
                        <?= lang('App.close'); ?>
                    </v-btn>
                    <v-btn color="primary" text link :href="'<?= base_url('barang/barcode?id_barang='); ?>' + idBarang + '&text=' + barcode + '&jumlah=' + jmlBarcode " target="_blank">
                        <?= lang('App.print'); ?> Barcode
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Barcode -->

<!-- Modal Label -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalLabel" persistent max-width="300">
            <v-card>
                <v-card-title class="text-h6 mb-3">
                    Jumlah
                </v-card-title>
                <v-card-text>
                    <v-text-field type="number" v-model="jmlLabel" label="Jumlah" hide-details outlined></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="closeLabel">
                        <?= lang('App.close'); ?>
                    </v-btn>
                    <v-btn color="primary" text link :href="'<?= base_url('barang/label?text='); ?>' + kodeBarang + '&jumlah=' + jmlLabel " target="_blank">
                        <?= lang('App.print'); ?> Label
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Label -->

<!-- Modal Kategori -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalKategori" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    Kategori
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalKategoriClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="7">
                                    <v-text-field label="<?= lang('App.categoryName') ?>" v-model="namaKategori" type="text" :error-messages="nama_kategoriError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="5">
                                    <v-btn color="primary" large @click="saveKategori" :loading="loading2"><?= lang('App.add') ?></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                    <v-data-table :headers="tbKategori" :items="dataKategori" :items-per-page="5" class="elevation-1" :loading="loading1">
                        <template v-slot:item="{ item }">
                            <tr>
                                <td>{{item.id_kategori}}</td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nama_kategori" @save="updateKategori(item)" @cancel="" @open="" @close="">
                                        {{item.nama_kategori}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nama_kategori" class="pt-3" append-icon="mdi-content-save" @click:append="updateKategori(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-btn color="error" icon @click="deleteKategori(item)" :loading="loading3">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalKategoriClose" elevation="1"><?= lang('App.close') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<!-- Modal Satuan -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalSatuan" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    Satuan
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalSatuanClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-form ref="form" v-model="valid">
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="5">
                                    <v-text-field label="Nama Satuan" v-model="namaSatuan" type="text" :error-messages="nama_satuanError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-text-field label="Nilai Satuan" v-model="nilaiSatuan" type="number" :error-messages="nilai_satuanError"></v-text-field>
                                </v-col>

                                <v-col cols="12" md="2">
                                    <v-btn color="primary" large @click="saveSatuan" :loading="loading2"><?= lang('App.add') ?></v-btn>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                    <v-data-table :headers="tbSatuan" :items="dataSatuan" :items-per-page="5" class="elevation-1" :loading="loading1">
                        <template v-slot:item="{ item }">
                            <tr>
                                <td>{{item.id_satuan}}</td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nama_satuan" @save="updateSatuan(item)" @cancel="" @open="" @close="">
                                        {{item.nama_satuan}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nama_satuan" class="pt-3" append-icon="mdi-content-save" @click:append="updateSatuan(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-edit-dialog large persistent :return-value.sync="item.nilai_satuan" @save="updateSatuan(item)" @cancel="" @open="" @close="">
                                        {{item.nilai_satuan}}
                                        <template v-slot:input>
                                            <v-text-field v-model="item.nilai_satuan" class="pt-3" append-icon="mdi-content-save" @click:append="updateSatuan(item)" outlined dense hide-details single-line></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </td>
                                <td>
                                    <v-btn color="error" icon @click="deleteSatuan(item)" :loading="loading3">
                                        <v-icon>mdi-close</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text large @click="modalSatuanClose" elevation="1"><?= lang('App.close') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<!-- Modal Transfer Stock -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalTransfer" persistent max-width="600px">
            <v-card>
                <v-card-title class="text-h5">
                    Transfer <?= lang('App.warehouse'); ?> &nbsp;<v-icon>mdi-swap-horizontal-bold</v-icon>&nbsp; Stok
                    <v-spacer></v-spacer>
                    <v-btn icon @click="transferStokClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-radio-group v-model="radioJenis" row class="pt-0 mb-3" :error-messages="jenisError">
                            <v-radio label="Masuk Stok Aktif" value="in"></v-radio>
                            <!-- <v-radio label="Keluar Stok" value="out"></v-radio> -->
                            <v-radio label="Masuk <?= lang('App.warehouse'); ?>" value="wh_in"></v-radio>
                        </v-radio-group>
                        <v-text-field v-model="kodeBarang" label="<?= lang('App.codeItem'); ?>" filled></v-text-field>
                        <v-text-field v-model="stok" type="number" label="<?= lang('App.stock'); ?>" filled></v-text-field>
                        <v-text-field v-model="stokGd" type="number" label="<?= lang('App.stock') . ' ' . lang('App.warehouse'); ?>" filled></v-text-field>
                        <v-text-field v-model="valueTransfer" type="number" label="Input Value" min="0" :error-messages="value_transferError" outlined></v-text-field>

                        <v-alert type="info" text dense outlined>
                            <span class="text-body-2 grey--text text--darken-4">
                                Masuk Stok: dari Gudang ke Stok Aktif<br />
                                <!-- Keluar Stok: dari Stok Aktif ke Gudang<br /> -->
                                Masuk Gudang: Tambah Stok Gudang
                            </span>
                        </v-alert>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" large @click="setStokTransfer" :loading="loading2" elevation="1"><v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

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

    // Initial Data
    dataVue = {
        ...dataVue,
        modalAdd: false,
        modalEdit: false,
        modalShow: false,
        modalDelete: false,
        modalDeleteMultiple: false,
        confirmDeleteMultiple: false,
        modalBarcode: false,
        modalLabel: false,
        modalKategori: false,
        modalSatuan: false,
        modalTransfer: false,
        search: "<?= $search; ?>",
        selected: [],
        dataTable: [{
            text: '',
            value: 'sku',
            sortable: false
        }, {
            text: '<?= lang('App.itemInfo') ?>',
            value: 'nama_barang'
        }, {
            text: 'BARCODE',
            value: 'barcode'
        }, {
            text: '<?= lang('App.unit') ?>',
            value: 'satuan_barang'
        }, {
            text: '<?= lang('App.priceBuy') ?>',
            value: 'harga_beli'
        }, {
            text: '<?= lang('App.priceSell') ?>',
            value: 'harga_jual'
        }, {
            text: '<?= lang('App.stock') ?>',
            value: 'stok'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'kode_barang',
            sortable: false
        }, ],
        dataBarang: [],
        totalData: 0,
        data: [],
        options: {},
        idBarang: "",
        kodeBarang: "",
        hargaBeli: "",
        harga_beliError: "",
        hargaJual: "",
        harga_jualError: "",
        stok: "",
        stokGd: "",
        dataKategori: [],
        barcode: "",
        jmlBarcode: 1,
        jmlLabel: 18,
        tabsBarang: 'semua-barang',
        tabColor: "primary",
        jmlSemuaBarang: 0,
        jmlStokHabis: 0,
        jmlNonaktif: 0,
        activeColor1: "primary",
        activeColor2: "",
        activeColor3: "",
        namaKategori: "",
        namaKategoriEdit: "",
        nama_kategoriError: "",
        tbKategori: [{
            text: 'ID',
            value: 'id_kategori'
        }, {
            text: 'Nama Kategori',
            value: 'nama_kategori'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        dataSatuan: [],
        namaSatuan: "",
        namaSatuanEdit: "",
        nama_satuanError: "",
        nilaiSatuan: "",
        nilaiSatuanEdit: "",
        nilai_satuanError: "",
        tbSatuan: [{
            text: 'ID',
            value: 'id_satuan'
        }, {
            text: 'Satuan',
            value: 'nama_satuan'
        }, {
            text: 'Nilai',
            value: 'nilai_satuan'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        kategori: [],
        satuan: [],
        editStok: false,
        radioJenis: "",
        jenisError: "",
        valueTransfer: "",
        value_transferError: ""
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        //axios.defaults.headers['Authorization'] = 'Bearer ' + token;
        this.getBarang();
        this.getJmlSemuaBarang();
        this.getJmlStokHabis();
        this.getJmlNonaktif();
        this.getKategori();
        this.getSatuan();
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

        tabsBarang: function() {
            if (this.tabsBarang == 'semua-barang') {
                this.tabColor = 'primary';
                if (this.kategori != []) {
                    this.getBarangFiltered();
                } else {
                    this.getBarang();
                }
            } else if (this.tabsBarang == 'stok-habis') {
                this.tabColor = 'error';
                if (this.kategori != []) {
                    this.getBarangHabisFiltered();
                } else {
                    this.getBarangHabis();
                }
            } else if (this.tabsBarang == 'non-aktif') {
                this.tabColor = 'grey darken-4';
                if (this.kategori != []) {
                    this.getBarangNonaktifFiltered();
                } else {
                    this.getBarangNonaktif();
                }
            }
        },
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

        toggle(isSelected, select, e) {
            select(!isSelected)
        },

        // Format Ribuan Rupiah versi 2
        Ribuan(key) {
            const format = key.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const rupiah = 'Rp' + convert.join('.').split('').reverse().join('');
            return rupiah;
        },

        // Handle Kategori Filter
        handleKategori: function() {
            if (this.tabsBarang == 'semua-barang') {
                this.getBarangFiltered();
            } else if (this.tabsBarang == 'stok-habis') {
                this.getBarangHabisFiltered();
            } else if (this.tabsBarang == 'non-aktif') {
                this.getBarangNonaktifFiltered();
            }
        },

        // Handle Clear
        handleClear: function() {
            this.kategori = [];
            if (this.tabsBarang == 'semua-barang') {
                this.getBarang();
            } else if (this.tabsBarang == 'stok-habis') {
                this.getBarangHabis();
            } else if (this.tabsBarang == 'non-aktif') {
                this.getBarangNonaktif();
            }
        },

        // Handle Search Submit Filter
        handleSubmit: function() {
            if (this.tabsBarang == 'semua-barang') {
                this.getBarang();
            } else if (this.tabsBarang == 'stok-habis') {
                this.getBarangHabis();
            } else if (this.tabsBarang == 'non-aktif') {
                this.getBarangNonaktif();
            }
        },

        // Get Barang
        getBarang: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Barang Filtered
        getBarangFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang?kategori=${this.kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Barang Stok Habis
        getBarangHabis: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang/get/stokhabis?kategori=${this.kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Barang Stok Habis Filtered
        getBarangHabisFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang/get/stokhabis?kategori=${this.kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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


        // Get Barang Nonaktif
        getBarangNonaktif: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang/get/nonaktif?kategori=${this.kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Barang Nonaktif Filtered
        getBarangNonaktifFiltered: function() {
            this.loading = true;
            axios.get(`<?= base_url(); ?>api/barang/get/nonaktif?kategori=${this.kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
                        //console.log(this.dataBarang);
                        this.selected = [];
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataBarang = data.data;
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

        // Get Jumlah Barang
        getJmlSemuaBarang: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/barang/get/jmlsemuabarang', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.jmlSemuaBarang = data.data;
                        //console.log(this.dataBarang);
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

        // Get Jumlah Barang Stok 0
        getJmlStokHabis: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/barang/get/jmlstokhabis', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.jmlStokHabis = data.data;
                        //console.log(this.dataBarang);
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

        // Get Jumlah Barang Active 0
        getJmlNonaktif: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/barang/get/jmlnonaktif', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.jmlNonaktif = data.data;
                        //console.log(this.dataBarang);
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
            setTimeout(() => window.location.href = `<?= base_url() ?>barang/${item.uuid_barang}/edit`, 100);
        },

        // Get Item Delete Barang
        deleteItem: function(item) {
            this.modalDelete = true;
            this.idBarang = item.id_barang;
            this.namaBarang = item.nama_barang;
        },

        // Delete Barang
        deleteBarang: function() {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/barang/delete/${this.idBarang}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getJmlSemuaBarang();
                        this.getJmlStokHabis();
                        this.getJmlNonaktif();
                        if (this.tabsBarang == 'semua-barang') {
                            this.tabsBarang == 'semua-barang';
                            if (this.kategori != '') {
                                this.getBarangFiltered();
                            } else {
                                this.getBarang();
                            }
                        } else if (this.tabsBarang == 'stok-habis') {
                            this.tabsBarang == 'stok-habis';
                            if (this.kategori != '') {
                                this.getBarangHabisFiltered();
                            } else {
                                this.getBarangHabis();
                            }
                        } else if (this.tabsBarang == 'non-aktif') {
                            this.tabsBarang == 'non-aktif';
                            if (this.kategori != '') {
                                this.getBarangNonaktifFiltered();
                            } else {
                                this.getBarangNonaktif();
                            }
                        }
                        this.modalDelete = false;
                        this.selected = [];
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

        // Set Harga Beli
        setHargaBeli: function(item) {
            this.loading = true;
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
                    }
                    this.getJmlSemuaBarang();
                    this.getJmlStokHabis();
                    this.getJmlNonaktif();
                    if (this.tabsBarang == 'semua-barang') {
                        this.tabsBarang == 'semua-barang';
                        if (this.kategori != '') {
                            this.getBarangFiltered();
                        } else {
                            this.getBarang();
                        }
                    } else if (this.tabsBarang == 'stok-habis') {
                        this.tabsBarang == 'stok-habis';
                        if (this.kategori != '') {
                            this.getBarangHabisFiltered();
                        } else {
                            this.getBarangHabis();
                        }
                    } else if (this.tabsBarang == 'non-aktif') {
                        this.tabsBarang == 'non-aktif';
                        if (this.kategori != '') {
                            this.getBarangNonaktifFiltered();
                        } else {
                            this.getBarangNonaktif();
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


        // Set Item Harga Jual
        setHargaJual: function(item) {
            this.loading = true;
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
                    }
                    this.getJmlSemuaBarang();
                    this.getJmlStokHabis();
                    this.getJmlNonaktif();
                    if (this.tabsBarang == 'semua-barang') {
                        this.tabsBarang == 'semua-barang';
                        if (this.kategori != '') {
                            this.getBarangFiltered();
                        } else {
                            this.getBarang();
                        }
                    } else if (this.tabsBarang == 'stok-habis') {
                        this.tabsBarang == 'stok-habis';
                        if (this.kategori != '') {
                            this.getBarangHabisFiltered();
                        } else {
                            this.getBarangHabis();
                        }
                    } else if (this.tabsBarang == 'non-aktif') {
                        this.tabsBarang == 'non-aktif';
                        if (this.kategori != '') {
                            this.getBarangNonaktifFiltered();
                        } else {
                            this.getBarangNonaktif();
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

        // Set Item Stok
        setStok: function(item) {
            this.loading = true;
            this.idBarang = item.id_barang;
            this.stok = item.stok;
            this.stokGd = item.stok_gudang;
            axios.put(`<?= base_url(); ?>api/barang/setstok/${this.idBarang}`, {
                    stok: this.stok,
                    stok_gudang: this.stokGd,
                    jenis: "aktif",
                    value_transfer: "null",
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getJmlSemuaBarang();
                        this.getJmlStokHabis();
                        this.getJmlNonaktif();
                        if (this.tabsBarang == 'semua-barang') {
                            this.tabsBarang == 'semua-barang';
                            if (this.kategori != '') {
                                this.getBarangFiltered();
                            } else {
                                this.getBarang();
                            }
                        } else if (this.tabsBarang == 'stok-habis') {
                            this.tabsBarang == 'stok-habis';
                            if (this.kategori != '') {
                                this.getBarangHabisFiltered();
                            } else {
                                this.getBarangHabis();
                            }
                        } else if (this.tabsBarang == 'non-aktif') {
                            this.tabsBarang == 'non-aktif';
                            if (this.kategori != '') {
                                this.getBarangNonaktifFiltered();
                            } else {
                                this.getBarangNonaktif();
                            }
                        }
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

        // Set Item Stok Transfer
        setStokTransfer: function() {
            this.loading2 = true;
            axios.put(`<?= base_url(); ?>api/barang/setstok/${this.idBarang}`, {
                    stok: this.stok,
                    stok_gudang: this.stokGd,
                    value_transfer: this.valueTransfer,
                    jenis: this.radioJenis,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalTransfer = false;
                        this.valueTransfer = "";
                        this.getJmlSemuaBarang();
                        this.getJmlStokHabis();
                        this.getJmlNonaktif();
                        if (this.tabsBarang == 'semua-barang') {
                            this.tabsBarang == 'semua-barang';
                            if (this.kategori != '') {
                                this.getBarangFiltered();
                            } else {
                                this.getBarang();
                            }
                        } else if (this.tabsBarang == 'stok-habis') {
                            this.tabsBarang == 'stok-habis';
                            if (this.kategori != '') {
                                this.getBarangHabisFiltered();
                            } else {
                                this.getBarangHabis();
                            }
                        } else if (this.tabsBarang == 'non-aktif') {
                            this.tabsBarang == 'non-aktif';
                            if (this.kategori != '') {
                                this.getBarangNonaktifFiltered();
                            } else {
                                this.getBarangNonaktif();
                            }
                        }
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
                    this.loading = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Set Item Aktif
        setAktif: function(item) {
            this.loading = true;
            this.idBarang = item.id_barang;
            this.active = item.active;
            axios.put(`<?= base_url(); ?>api/barang/setaktif/${this.idBarang}`, {
                    active: this.active,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getJmlSemuaBarang();
                        this.getJmlStokHabis();
                        this.getJmlNonaktif();
                        if (this.tabsBarang == 'semua-barang') {
                            this.tabsBarang == 'semua-barang';
                            if (this.kategori != '') {
                                this.getBarangFiltered();
                            } else {
                                this.getBarang();
                            }
                        } else if (this.tabsBarang == 'stok-habis') {
                            this.tabsBarang == 'stok-habis';
                            if (this.kategori != '') {
                                this.getBarangHabisFiltered();
                            } else {
                                this.getBarangHabis();
                            }
                        } else if (this.tabsBarang == 'non-aktif') {
                            this.tabsBarang == 'non-aktif';
                            if (this.kategori != '') {
                                this.getBarangNonaktifFiltered();
                            } else {
                                this.getBarangNonaktif();
                            }
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

        // Export Excel
        excelMultiple: function(selected) {
            this.loading4 = true;
            var data = JSON.stringify(selected);
            //console.log(data);
            axios.post(`<?= base_url(); ?>api/excel/exporttoexcel`, {
                    data
                }, options)
                .then(res => {
                    // handle success
                    this.loading4 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        // download file
                        const url = data.data.url;
                        window.location.href = url;
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

        // Confirm Delete
        confirmDelete: function(selected) {
            this.modalDeleteMultiple = true;
            this.deleted = JSON.stringify(selected);;
            //console.log(this.deleted);
        },

        // Delete Multi
        deleteMultiple: function() {
            var data = this.deleted;
            this.loading = true;
            axios.post(`<?= base_url(); ?>api/barang/delete/multiple`, {
                    data
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getJmlSemuaBarang();
                        this.getJmlStokHabis();
                        this.getJmlNonaktif();
                        if (this.tabsBarang == 'semua-barang') {
                            this.tabsBarang == 'semua-barang';
                            if (this.kategori != '') {
                                this.getBarangFiltered();
                            } else {
                                this.getBarang();
                            }
                        } else if (this.tabsBarang == 'stok-habis') {
                            this.tabsBarang == 'stok-habis';
                            if (this.kategori != '') {
                                this.getBarangHabisFiltered();
                            } else {
                                this.getBarangHabis();
                            }
                        } else if (this.tabsBarang == 'non-aktif') {
                            this.tabsBarang == 'non-aktif';
                            if (this.kategori != '') {
                                this.getBarangNonaktifFiltered();
                            } else {
                                this.getBarangNonaktif();
                            }
                        }
                        this.modalDeleteMultiple = false;
                        this.selected = [];
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

        // Sum Total
        sumTotal(key) {
            // sum data in give key (property)
            let total = 0
            const sum = this.keranjang.reduce((accumulator, currentValue) => {
                return (total += +currentValue[key])
            }, 0)
            this.total = sum;
            return sum
        },

        // Modal Barcode
        openBarcode: function(item) {
            this.modalBarcode = true;
            this.idBarang = item.id_barang;
            this.barcode = item.barcode;
        },
        closeBarcode: function(item) {
            this.modalBarcode = false;
            this.jmlBarcode = 1;
        },

        // Get Kategori
        getKategori: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>api/kategori', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataKategori = data.data;
                        //console.log(this.dataKategori);
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataKategori = data.data;
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

        // Modal Kategori
        addKategori: function() {
            this.modalKategori = true;
        },
        modalKategoriClose: function() {
            this.modalKategori = false;
            this.$refs.form.resetValidation();
        },

        // Save Kategori
        saveKategori: function() {
            this.loading2 = true;
            axios.post(`<?= base_url(); ?>api/kategori/save`, {
                    nama_kategori: this.namaKategori,
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.namaKategori = "";
                        this.getKategori();
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

        // Update Kategori
        updateKategori: function(item) {
            this.loading1 = true;
            this.namaKategoriEdit = item.nama_kategori;
            axios.put(`<?= base_url(); ?>api/kategori/update/${item.id_kategori}`, {
                    nama_kategori: this.namaKategoriEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKategori();
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

        // Delete Kategori
        deleteKategori: function(item) {
            this.loading3 = true;
            axios.delete(`<?= base_url(); ?>api/kategori/delete/${item.id_kategori}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKategori();
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

        // Get Satuan
        getSatuan: function() {
            this.loading1 = true;
            axios.get('<?= base_url(); ?>api/satuan', options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.dataSatuan = data.data;
                    } else {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataSatuan = data.data;
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

        // Modal Satuan
        addSatuan: function() {
            this.modalSatuan = true;
        },
        modalSatuanClose: function() {
            this.modalSatuan = false;
            this.$refs.form.resetValidation();
        },

        // Save Satuan
        saveSatuan: function() {
            this.loading2 = true;
            axios.post(`<?= base_url(); ?>api/satuan/save`, {
                    nama_satuan: this.namaSatuan,
                    nilai_satuan: this.nilaiSatuan
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.namaSatuan = "";
                        this.nilaiSatuan = "";
                        this.getSatuan();
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

        // Update Satuan
        updateSatuan: function(item) {
            this.loading1 = true;
            this.namaSatuanEdit = item.nama_satuan;
            this.nilaiSatuanEdit = item.nilai_satuan;
            axios.put(`<?= base_url(); ?>api/satuan/update/${item.id_satuan}`, {
                    nama_satuan: this.namaSatuanEdit,
                    nilai_satuan: this.nilaiSatuanEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading1 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getSatuan();
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

        // Delete Satuan
        deleteSatuan: function(item) {
            this.loading3 = true;
            axios.delete(`<?= base_url(); ?>api/satuan/delete/${item.id_satuan}`, options)
                .then(res => {
                    // handle success
                    this.loading3 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getSatuan();
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

        // Transfer Stock
        transferStok: function(item) {
            this.modalTransfer = true;
            this.radioJenis = "";
            this.idBarang = item.id_barang;
            this.kodeBarang = item.kode_barang;
            this.stok = item.stok;
            this.stokGd = item.stok_gudang;
            //item.stok = (parseInt(item.stok) + parseInt(item.stok_gudang));
            //item.stok_gudang = 0;
        },
        transferStokClose: function() {
            this.modalTransfer = false;
            this.radioJenis = "";
            this.valueTransfer = "";
        },

        // Barcode Multiple
        barcodeMultiple: function(selected) {
            this.loading6 = true;
            if (selected.length > 18) {
                this.snackbar = true;
                this.snackbarMessage = "Jumlah dipilih harus <= 18";
                this.loading6 = false;
                return;
            }
            var data = JSON.stringify(selected);
            axios.post(`<?= base_url(); ?>api/barang/barcode_multiple`, {
                    data
                }, options)
                .then(res => {
                    // handle success
                    this.loading6 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        // download file
                        setTimeout(() => window.open(data.data.url, '_blank'), 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                    }
                })
                .catch(err => {
                    // handle error
                    console.log(err);
                    this.loading6 = false;
                    var error = err.response
                    if (error.data.expired == true) {
                        this.snackbar = true;
                        this.snackbarMessage = error.data.message;
                        setTimeout(() => window.location.href = error.data.data.url, 1000);
                    }
                })
        },

        // Modal Label
        openLabel: function(item) {
            this.modalLabel = true;
            this.kodeBarang = item.kode_barang;
            this.barcode = item.barcode;
        },
        closeLabel: function(item) {
            this.modalLabel = false;
            this.jmlBarcode = 1;
        },

        // Label Multiple
        labelMultiple: function(selected) {
            this.loading5 = true;
            if (selected.length > 18) {
                this.snackbar = true;
                this.snackbarMessage = "Jumlah dipilih harus <= 18";
                this.loading5 = false;
                return;
            }
            var data = JSON.stringify(selected);
            axios.post(`<?= base_url(); ?>api/barang/label_multiple`, {
                    data
                }, options)
                .then(res => {
                    // handle success
                    this.loading5 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        // download file
                        setTimeout(() => window.open(data.data.url, '_blank'), 1000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
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