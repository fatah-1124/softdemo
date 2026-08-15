<v-data-table :headers="thKategori" :items="dataKategoriWithIndex" :items-per-page="-1" class="elevation-1" :loading="loading">
    <template v-slot:item="{ item }">
        <tr>
            <td>{{item.index}}</td>
            <td><a @click="showItem(item)">{{item.nama_kategori}}</a></td>
            <td>{{item.qty}}</td>
            <td>{{Ribuan(item.jumlah)}}</td>
        </tr>
    </template>
    <template v-slot:footer.prepend>
        <v-btn outlined :href="'<?= base_url('laporan/kategori-pdf') ?>' + '?tgl_start=' + startDate + '&tgl_end=' + endDate" target="_blank" v-show="dataKategori != ''">
            <v-icon>mdi-download</v-icon> PDF
        </v-btn>
    </template>
</v-data-table>

<template>
    <v-dialog v-model="modalShow" scrollable max-width="1200">
        <v-card>
            <v-card-title class="text-h5">
                Laporan Detail dari Kategori {{namaKategori}}
                <v-spacer></v-spacer>
                <v-btn icon @click="modalShow = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-data-table :headers="thDetailKategori" :items="dataDetailKategori" :items-per-page="-1" class="elevation-1 mt-4" :loading="loading1">
                    <template v-slot:item="{ item }">
                        <tr>
                            <td>{{item.faktur}}</td>
                            <td>{{dayjs(item.created_at).format('DD-MM-YYYY HH:mm')}}</td>
                            <td>{{Ribuan(item.diskon)}}</td>
                            <td>{{Ribuan(item.total)}}</td>
                            <td>{{item.nama_barang}}</td>
                            <td>{{item.qty}}</td>
                            <td>{{item.satuan}}</td>
                            <td>{{Ribuan(item.jumlah)}}</td>
                            <td>{{Ribuan(item.pajak)}}</td>
                            <td>{{Ribuan(item.pembulatan)}}</td>
                        </tr>
                    </template>
                </v-data-table>
            </v-card-text>
            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn large color="primary" text @click="modalShow = false" elevation="1">
                    OK
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>