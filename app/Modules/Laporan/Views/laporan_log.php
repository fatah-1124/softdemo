<v-card-title>
    <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable>
    </v-text-field>
</v-card-title>
<v-data-table :headers="thLog" :items="dataLogWithIndex" :items-per-page="10" :search="search" class="elevation-1" :loading="loading">
    <template v-slot:item="{ item }">
        <tr>
            <td>{{item.id_log}}</td>
            <td>{{item.keterangan}}</td>
            <td>{{item.created_at}}</td>
        </tr>
    </template>
    <template v-slot:footer.prepend>
        <!-- <v-btn outlined :href="'<?= base_url('laporan/log-pdf') ?>' + '?tgl_start=' + startDate + '&tgl_end=' + endDate" target="_blank" v-show="dataLog != ''">
            <v-icon>mdi-download</v-icon> PDF
        </v-btn> -->
    </template>
</v-data-table>