<v-data-table :headers="thBarang" :items="dataBarangWithIndex" :items-per-page="-1" class="elevation-1" :loading="loading" ref="testHtml">
    <template v-slot:item="{ item }">
        <tr>
            <td>{{item.index}}</td>
            <td>{{dayjs(item.created_at).format('DD-MM-YYYY')}}</td>
            <td>{{item.kode_barang}}</td>
            <td>{{item.nama_barang}}</td>
            <td>{{item.satuan}}</td>
            <td>{{item.qty}}</td>
            <td>{{Ribuan(item.harga_jual)}}</td>
            <td>{{Ribuan(item.jumlah)}}</td>
            <td>{{Ribuan(item.ppn)}}</td>
        </tr>
    </template>
    <template slot="body.append">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">Total</td>
            <td>{{ sumTotalBarang('qty') }}</td>
            <td></td>
            <td>{{ Ribuan(sumTotalBarang('jumlah')) }}</td>
            <td>{{ Ribuan(sumTotalBarang('ppn')) }}</td>
        </tr>
    </template>
    <template v-slot:footer.prepend>
        <v-btn outlined :href="'<?= base_url('laporan/barang-pdf') ?>' + '?tgl_start=' + startDate + '&tgl_end=' + endDate" target="_blank" v-show="dataBarang != ''">
            <v-icon>mdi-download</v-icon> PDF
        </v-btn>
    </template>
</v-data-table>