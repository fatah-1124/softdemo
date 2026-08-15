<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title; ?></h1>
    <v-card>
        <v-card-title>
            <v-btn color="primary" dark @click="modalAddOpen" large elevation="1">
                <v-icon>mdi-plus</v-icon> <?= lang('App.add') ?>
            </v-btn>
            <v-spacer></v-spacer>
            <v-select v-model="search" label="Filter Tipe" :items="dataTipe" single-line prepend-icon="mdi-filter" @change="handleSubmit" class="mr-3" style="max-width: 250px !important;"></v-select>

            <v-text-field v-model="search" v-on:keydown.enter="handleSubmit" @click:clear="handleSubmit" append-icon="mdi-magnify" label="<?= lang("App.search") ?>" single-line clearable></v-text-field>
        </v-card-title>
        <v-data-table :headers="datatable" :items="data" :options.sync="options" :server-items-length="totalData" :items-per-page="10" :loading="loading" :search="search" class="elevation-1" loading-text="<?= lang('App.loadingWait'); ?>" dense>
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.id_kontak}}</td>
                    <td>{{item.nama}}</td>
                    <td>{{item.tipe}}</td>
                    <td>{{formatMyString(item.grup)}}</td>
                    <td>{{item.perusahaan}}</td>
                    <td>{{item.telepon}}</td>
                    <td>{{item.email}}</td>
                    <td>{{item.alamat}}</td>
                    <td>
                        <v-btn icon color="primary" class="mr-3" @click="editItem(item)">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon color="red" @click="deleteItem(item)" :disabled="item.id_kontak == 1">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
    <!-- End Table List -->
</template>

<!-- Modal Add -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalAdd" persistent scrollable max-width="900px">
            <v-card>
                <v-card-title><?= lang('App.add') ?> Kontak
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalAddClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form v-model="valid" ref="form">
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-select v-model="tipe" label="Tipe Kontak" :items="dataTipe" item-text="text" item-value="value" :error-messages="tipeError" :loading="loading2" outlined></v-select>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select v-model="grup" label="Group" :items="dataGroup" item-text="text" item-value="value" :error-messages="grupError" :loading="loading2" clearable outlined></v-select>
                            </v-col>
                        </v-row>

                        <v-text-field v-model="nama" label="Nama" :error-messages="namaError" outlined></v-text-field>

                        <v-text-field v-model="perusahaan" label="Perusahaan" :error-messages="perusahaanError" outlined></v-text-field>

                        <v-text-field v-model="alamat" label="Alamat" :error-messages="alamatError" outlined></v-text-field>

                        <v-row>
                            <v-col>
                                <v-text-field type="number" label="Telepon" v-model="telepon" :error-messages="teleponError" hint="Format 62" persistent-hint outlined></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field v-model="email" :rules="[rules.email]" label="E-mail" :error-messages="emailError" outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <v-text-field type="number" label="NIK KTP" v-model="nikktp" :error-messages="nikktpError" outlined></v-text-field>

                        <v-text-field label="NPWP" v-model="npwp" :error-messages="npwpError" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="saveKontak" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.save') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Add -->

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent scrollable max-width="900px">
            <v-card>
                <v-card-title><?= lang('App.edit') ?> {{namaEdit}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-5">
                    <v-form ref="form" v-model="valid">
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-select v-model="tipeEdit" label="Tipe Kontak" :items="dataTipe" item-text="text" item-value="value" :error-messages="tipeError" :loading="loading2" outlined></v-select>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select v-model="grupEdit" label="Group" :items="dataGroup" item-text="text" item-value="value" :error-messages="grupError" :loading="loading2" clearable outlined></v-select>
                            </v-col>
                        </v-row>

                        <v-text-field v-model="namaEdit" label="Nama" :error-messages="namaError" outlined></v-text-field>

                        <v-text-field v-model="perusahaanEdit" label="Perusahaan" :error-messages="perusahaanError" outlined></v-text-field>

                        <v-text-field v-model="alamatEdit" label="Alamat" :error-messages="alamatError" outlined></v-text-field>

                        <v-row>
                            <v-col>
                                <v-text-field type="number" label="Telepon" v-model="teleponEdit" :error-messages="teleponError" hint="Format 62" persistent-hint outlined></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field v-model="emailEdit" :rules="[rules.email]" label="E-mail" :error-messages="emailError" outlined></v-text-field>
                            </v-col>
                        </v-row>

                        <v-text-field type="number" label="NIK KTP" v-model="nikktpEdit" :error-messages="nikktpError" outlined></v-text-field>

                        <v-text-field label="NPWP" v-model="npwpEdit" :error-messages="npwpError" outlined></v-text-field>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="primary" @click="updateKontak" :loading="loading" elevation="1">
                        <v-icon>mdi-content-save</v-icon> <?= lang('App.update') ?>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

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
                    <v-btn text large @click="modalDelete = false" elevation="1"><?= lang("App.no") ?></v-btn>
                    <v-btn color="error" dark large @click="deleteKontak" :loading="loading" elevation="1"><?= lang("App.yes") ?></v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Delete -->
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
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
        search: "",
        datatable: [{
            text: '#',
            value: 'id_kontak'
        }, {
            text: 'Nama',
            value: 'nama'
        }, {
            text: 'Tipe',
            value: 'tipe'
        }, {
            text: 'Group',
            value: 'grup'
        }, {
            text: 'Perusahaan',
            value: 'perusahaan'
        }, {
            text: 'Telepon',
            value: 'telepon'
        }, {
            text: 'E-mail',
            value: 'email'
        }, {
            text: 'Alamat',
            value: 'alamat'
        }, {
            text: '<?= lang('App.action') ?>',
            value: 'actions',
            sortable: false
        }, ],
        dataKontak: [],
        totalData: 0,
        data: [],
        options: {},
        dataTipe: [{
            text: 'Pelanggan',
            value: 'Pelanggan'
        }, {
            text: 'Vendor/Supplier',
            value: 'Vendor'
        }],
        tipe: "",
        tipeError: "",
        dataGroup: [{
            text: 'Member',
            value: 'member'
        }],
        grup: "",
        grupError: "",
        nama: "",
        namaError: "",
        perusahaan: "",
        perusahaanError: "",
        alamat: "",
        alamatError: "",
        telepon: "",
        teleponError: "",
        email: "",
        emailError: "",
        nikktp: "",
        nikktpError: "",
        npwp: "",
        npwpError: "",
        idKontakEdit: "",
        tipeEdit: "",
        grupEdit: "",
        namaEdit: "",
        perusahaanEdit: "",
        alamatEdit: "",
        teleponEdit: "",
        emailEdit: "",
        nikktpEdit: "",
        npwpEdit: "",
        idKontakDelete: "",
        namaDelete: "",
    }

    // Vue Created
    createdVue = function() {
        this.getKontak();
    }

    // Vue Computed
    computedVue = {
        ...computedVue,
        passwordMatch() {
            return () => this.password === this.verify || "<?= lang('App.samePassword') ?>";
        }
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

        dataKontak: function() {
            if (this.dataKontak != '') {
                // Call server-side paginate and sort
                this.getDataFromApi();
            }
        }
    }

    // Vue Methods
    methodsVue = {
        ...methodsVue,
        formatMyString(str) {
            if (!str) return '';
            let formattedStr = str.replace(/_/g, ' ');
            formattedStr = formattedStr.charAt(0).toUpperCase() + formattedStr.slice(1);
            return formattedStr;
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

                let items = this.dataKontak
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

        // Modal Open
        modalAddOpen: function() {
            this.modalAdd = true;
        },
        modalAddClose: function() {
            this.modalAdd = false;
            this.$refs.form.resetValidation();
        },

        // Handle Submit Filter
        handleSubmit: function() {
            this.getKontak();
        },

        // Get User Kontak
        getKontak: function() {
            this.loading = true;
            axios.get('<?= base_url(); ?>api/kontak', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.dataKontak = data.data;
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.dataKontak = data.data;
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

        // Save Kontak
        saveKontak: function() {
            this.loading = true;
            axios.post('<?= base_url(); ?>api/kontak/save', {
                    tipe: this.tipe,
                    grup: this.grup,
                    nama: this.nama,
                    perusahaan: this.perusahaan,
                    alamat: this.alamat,
                    telepon: this.telepon,
                    email: this.email,
                    nikktp: this.nikktp,
                    npwp: this.npwp,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKontak();
                        this.tipe = "";
                        this.grup = "";
                        this.nama = "";
                        this.perusahaan = "";
                        this.alamat = "";
                        this.telepon = "";
                        this.email = "";
                        this.nikktp = "";
                        this.npwp = "";
                        this.modalAdd = false;
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
                        this.modalAdd = true;
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

        // Get Item Kontak
        editItem: function(item) {
            this.modalEdit = true;
            this.idKontakEdit = item.id_kontak;
            this.tipeEdit = item.tipe;
            this.grupEdit = item.grup;
            this.namaEdit = item.nama;
            this.perusahaanEdit = item.perusahaan;
            this.alamatEdit = item.alamat;
            this.teleponEdit = item.telepon;
            this.emailEdit = item.email;
            this.nikktpEdit = item.nikktp;
            this.npwpEdit = item.npwp
        },
        modalEditClose: function() {
            this.modalEdit = false;
            this.$refs.form.resetValidation();
        },

        //Update Kontak
        updateKontak: function() {
            this.loading = true;
            axios.put(`<?= base_url(); ?>api/kontak/update/${this.idKontakEdit}`, {
                    tipe: this.tipeEdit,
                    grup: this.grupEdit,
                    nama: this.namaEdit,
                    perusahaan: this.perusahaanEdit,
                    alamat: this.alamatEdit,
                    telepon: this.teleponEdit,
                    email: this.emailEdit,
                    nikktp: this.nikktpEdit,
                    npwp: this.npwpEdit,
                }, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKontak();
                        this.modalEdit = false;
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
                    this.loading = false;
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
            this.idKontakDelete = item.id_kontak;
            this.namaDelete = item.nama;
        },

        // Delete Kontak
        deleteKontak: function() {
            this.loading = true;
            axios.delete(`<?= base_url(); ?>api/kontak/delete/${this.idKontakDelete}`, options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.getKontak();
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