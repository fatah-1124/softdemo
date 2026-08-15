<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title ?></span></h1>
    <v-card>
        <v-card-title>
            <v-btn large color="primary" dark @click="modalAddOpen" class="mr-3" elevation="1">
                <v-icon>mdi-plus</v-icon> <?= lang('App.add'); ?>
            </v-btn>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable @click:clear="handleSubmit">
            </v-text-field>
        </v-card-title>
        <v-data-table :headers="dataTable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>">
            <template v-slot:top>

            </template>
            <template v-slot:item="{ item }">
                <tr>
                    <td width="100">{{item.id_group}}</td>
                    <td><a class="text-subtitle-1 font-weight-medium" :href="'<?= base_url('group/edit/'); ?>' + item.id_group">{{item.nama_group}}</a></td>
                    <td>{{item.updated_at}}</td>
                    <td>
                        <v-btn icon color="primary" class="mr-2" :href="'<?= base_url('group/edit/'); ?>' + item.id_group" title="Edit" alt="Edit">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon color="error" @click="deleteItem(item)" title="Delete" alt="Delete" :disabled="item.id_group == '1'">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
</template>

<!-- Modal Add Open -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent scrollable max-width="500px">
            <v-card>
                <v-card-title><?= lang('App.add'); ?> <?= $title; ?>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form v-model="valid" ref="form">
                        <v-text-field type="text" v-model="namaGroup" label="Nama Group" :error-messages="nama_groupError" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveGroup" :loading="loading3" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Add Open -->

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
                        <h2 class="font-weight-regular"><?= lang('App.delConfirm'); ?></h2>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="modalDelete = false" large elevation="1"><?= lang('App.close'); ?></v-btn>
                    <v-btn color="red" dark @click="deleteGroup" :loading="loading" elevation="1" large><?= lang('App.delete'); ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->

<!-- Loading2 -->
<v-dialog v-model="loading2" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
<!-- -->

<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
   /*  function unserialize(data) {
        var that = this,
            utf8Overhead = function(chr) {
                // http://phpjs.org/functions/unserialize:571#comment_95906
                var code = chr.charCodeAt(0);
                if (code < 0x0080) {
                    return 0;
                }
                if (code < 0x0800) {
                    return 1;
                }
                return 2;
            },
            error = function(type, msg, filename, line) {
                throw new that.window[type](msg, filename, line);
            },
            read_until = function(data, offset, stopchr) {
                var i = 2,
                    buf = [],
                    chr = data.slice(offset, offset + 1);

                while (chr != stopchr) {
                    if ((i + offset) > data.length) {
                        error('Error', 'Invalid');
                    }
                    buf.push(chr);
                    chr = data.slice(offset + (i - 1), offset + i);
                    i += 1;
                }
                return [buf.length, buf.join('')];
            },
            read_chrs = function(data, offset, length) {
                var i, chr, buf;

                buf = [];
                for (i = 0; i < length; i++) {
                    chr = data.slice(offset + (i - 1), offset + i);
                    buf.push(chr);
                    length -= utf8Overhead(chr);
                }
                return [buf.length, buf.join('')];
            },
            _unserialize = function(data, offset) {
                var dtype, dataoffset, keyandchrs, keys, contig,
                    length, array, readdata, readData, ccount,
                    stringlength, i, key, kprops, kchrs, vprops,
                    vchrs, value, chrs = 0,
                    typeconvert = function(x) {
                        return x;
                    };

                if (!offset) {
                    offset = 0;
                }
                dtype = (data.slice(offset, offset + 1)).toLowerCase();

                dataoffset = offset + 2;

                switch (dtype) {
                    case 'i':
                        typeconvert = function(x) {
                            return parseInt(x, 10);
                        };
                        readData = read_until(data, dataoffset, ';');
                        chrs = readData[0];
                        readdata = readData[1];
                        dataoffset += chrs + 1;
                        break;
                    case 'b':
                        typeconvert = function(x) {
                            return parseInt(x, 10) !== 0;
                        };
                        readData = read_until(data, dataoffset, ';');
                        chrs = readData[0];
                        readdata = readData[1];
                        dataoffset += chrs + 1;
                        break;
                    case 'd':
                        typeconvert = function(x) {
                            return parseFloat(x);
                        };
                        readData = read_until(data, dataoffset, ';');
                        chrs = readData[0];
                        readdata = readData[1];
                        dataoffset += chrs + 1;
                        break;
                    case 'n':
                        readdata = null;
                        break;
                    case 's':
                        ccount = read_until(data, dataoffset, ':');
                        chrs = ccount[0];
                        stringlength = ccount[1];
                        dataoffset += chrs + 2;

                        readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
                        chrs = readData[0];
                        readdata = readData[1];
                        dataoffset += chrs + 2;
                        if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
                            error('SyntaxError', 'String length mismatch');
                        }
                        break;
                    case 'a':
                        readdata = {};

                        keyandchrs = read_until(data, dataoffset, ':');
                        chrs = keyandchrs[0];
                        keys = keyandchrs[1];
                        dataoffset += chrs + 2;

                        length = parseInt(keys, 10);
                        contig = true;

                        for (i = 0; i < length; i++) {
                            kprops = _unserialize(data, dataoffset);
                            kchrs = kprops[1];
                            key = kprops[2];
                            dataoffset += kchrs;

                            vprops = _unserialize(data, dataoffset);
                            vchrs = vprops[1];
                            value = vprops[2];
                            dataoffset += vchrs;

                            if (key !== i)
                                contig = false;

                            readdata[key] = value;
                        }

                        if (contig) {
                            array = new Array(length);
                            for (i = 0; i < length; i++)
                                array[i] = readdata[i];
                            readdata = array;
                        }

                        dataoffset += 1;
                        break;
                    default:
                        error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
                        break;
                }
                return [dtype, dataoffset - offset, typeconvert(readdata)];
            };

        return _unserialize((data + ''), 0)[2];
    } */

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
        modalAdd: false,
        modalEdit: false,
        modalDelete: false,
        modalShow: false,
        menu: false,
        startDate: "",
        endDate: "",
        search: "",
        dataGroups: [],
        totalData: 0,
        data: [],
        options: {},
        dataTable: [{
            text: '#',
            value: 'id_group'
        }, {
            text: 'Group',
            value: 'nama_group'
        }, {
            text: '<?= lang('App.date'); ?> Update',
            value: 'updated_at'
        }, {
            text: 'Aksi',
            value: 'actions',
            sortable: false
        }, ],
        idGroup: "",
        namaGroup: "",
        nama_groupError: "",
        permission: [],

    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getGroups();
    }

    watchVue = {
        ...watchVue,
        options: {
            handler() {
                this.getDataFromApi()
            },
            deep: true,
        },

        dataGroups: function() {
            if (this.dataGroups != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        },
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        toggle(isSelected, select, e) {
            select(!isSelected)
        },

        // Handle Submit Filter
        handleSubmit: function() {
            this.getGroups();
        },

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

                let items = this.dataGroups
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

        // Get OpenClose
        getGroups: function() {
            this.loading = true;
            axios.get(`<?= base_url() ?>api/groups`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataGroups = data.data;
                        //console.log(this.dataGroups);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataGroups = data.data;
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

        // Modal Add Open
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        // Save Group
        saveGroup: function() {
            this.loading3 = true;
            axios.post('<?= base_url(); ?>api/group/save', {
                    nama_group: this.namaGroup
                }, options)
                .then(res => {
                    // handle success
                    this.loading3 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getGroups();
                        this.namaGroup = "";
                        this.modalAdd = false;
                        this.$refs.form.resetValidation();
                        setTimeout(() => window.location.href = data.data.url, 1000);
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
                        this.modalAdd = true;
                        this.$refs.form.resetValidation();
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

       

        // Get Item Delete
        deleteItem: function(item) {
            this.modalDelete = true;
            this.idGroup = item.id_group;
        },

        // Delete
        deleteGroup: function() {
            this.loading = true;
            axios.delete(`<?= base_url() ?>api/group/delete/${this.idGroup}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getGroups();
                        this.modalDelete = false;
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

    }
</script>
<?php $this->endSection("js") ?>