<v-data-table :headers="thBarangStok" :items="dataStokWithIndex" :items-per-page="-1" class="elevation-1" :loading="loading" ref="testHtml">
    <template v-slot:item="{ item }">
        <tr>
            <td>{{item.index}}</td>
            <td>{{item.kode_barang}}</td>
            <td>{{item.barcode}}</td>
            <td>{{item.nama_barang}}<br />SKU: {{item.sku ?? "-"}}</td>
            <td>{{item.satuan_barang}}/{{item.satuan_nilai}}</td>
            <td>{{item.stok}}</td>
        </tr>
    </template>
    <template v-slot:footer.prepend>
        <v-btn outlined :href="'<?= base_url('laporan/stokbarang-pdf') ?>' + '?tgl_start=' + startDate + '&tgl_end=' + endDate" target="_blank" v-show="dataBarang != ''">
            <v-icon>mdi-download</v-icon> PDF
        </v-btn>
    </template>
</v-data-table>