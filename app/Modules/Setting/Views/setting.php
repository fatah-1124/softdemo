<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title; ?></h1>
    <v-card>
        <v-card-title>
            <v-spacer></v-spacer>
            <v-text-field v-model="search" append-icon="mdi-magnify" label="<?= lang('App.search') ?>" single-line hide-details clearable>
            </v-text-field>
        </v-card-title>
        <!-- Table -->
        <v-data-table :headers="dataTable" :items="settingData" :items-per-page="-1" :loading="loading" :search="search" loading-text="<?= lang('App.loadingWait'); ?>">
            <template v-slot:item="{ item }">
                <tr>
                    <td>{{item.id}}</td>
                    <td>{{item.group_setting}}</td>
                    <td>{{item.variable_setting}}</td>
                    <td>{{item.value_setting}}</td>
                    <td>{{item.deskripsi_setting}}</td>
                    <td>{{item.updated_at}}</td>
                    <td>
                        <div v-if="item.group_setting == 'image'">
                            <v-btn color="primary" @click="editItem(item)" icon>
                                <v-icon>mdi-camera</v-icon>
                            </v-btn>
                        </div>
                        <!-- <div v-else-if="item.variable_setting == 'app_name' || item.variable_setting == 'app_version' || item.variable_setting == 'app_release' || item.variable_setting == 'app_developer'">
                        </div> -->
                        <div v-else>
                            <v-btn color="primary" @click="editItem(item)" icon>
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </div>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <!-- End Table -->
    </v-card>
</template>

<!-- Modal Edit -->
<template>
    <v-row justify="center">
        <v-dialog v-model="modalEdit" persistent scrollable width="600px">
            <v-card>
                <v-card-title>Edit {{deskripsiEdit}}
                    <v-spacer></v-spacer>
                    <v-btn icon @click="modalEditClose">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="py-3">
                    <v-form ref="form" v-model="valid">
                        <v-alert v-if="notifType != ''" dismissible dense outlined :type="notifType">{{notifMessage}}</v-alert>
                        <p class="mb-2 text-subtitle-1">Deskripsi Setting</p>
                        <v-text-field v-model="deskripsiEdit" :error-messages="deskripsi_settingError" outlined disabled></v-text-field>
                        <p class="mb-2 text-subtitle-1">Value Setting</p>
                        <div v-if="variableEdit == 'img_logo' || variableEdit == 'img_favicon' || variableEdit == 'img_background'">
                            <img v-bind:src="'<?= base_url() ?>' + valueEdit" width="150" class="mb-2" />
                            <v-file-input v-model="image" show-size label="Browse file" id="file" class="mb-2" accept=".jpg, .jpeg, .png" prepend-icon="mdi-camera" @change="onFileChange" @click:clear="onFileClear" :loading="loading2"></v-file-input>
                            <v-img :src="imagePreview" max-width="100">
                                <v-overlay v-model="overlay" absolute :opacity="0.1">
                                    <v-btn small class="ma-2" color="success" dark>
                                        OK
                                        <v-icon dark right>
                                            mdi-checkbox-marked-circle
                                        </v-icon>
                                    </v-btn>
                                </v-overlay>
                            </v-img>
                        </div>
                        <div v-else-if="variableEdit == 'img_logo_resize'">
                            <img v-bind:src="'<?= base_url() ?>' + valueEdit" width="150" class="mb-2" />
                        </div>
                        <div v-else-if="variableEdit == 'snackbars_position'">
                            <v-select v-model="valueEdit" :items="dataPosition" label="" item-text="text" item-value="value" :error-messages="value_settingError" outlined>
                            </v-select>
                        </div>
                        <div v-else-if="variableEdit == 'enable_frontend'">
                            <v-select v-model="valueEdit" :items="dataTrueFalse" label="" item-text="text" item-value="value" :error-messages="value_settingError" outlined>
                            </v-select>
                        </div>
                        <div v-else-if="variableEdit == 'cashierpay_position'">
                            <v-select v-model="valueEdit" :items="dataLeftRight" label="" item-text="text" item-value="value" :error-messages="value_settingError" outlined>
                            </v-select>
                        </div>
                        <div v-else-if="variableEdit == 'navbar_color' || variableEdit == 'sidebar_color'">
                            <v-select v-model="valueEdit" :items="dataTheme" item-text="text" item-value="value" :error-messages="value_settingError" outlined>
                            </v-select>
                        </div>
                        <div v-else>
                            <v-textarea v-model="valueEdit" :error-messages="value_settingError" rows="3" outlined></v-textarea>
                        </div>
                    </v-form>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <div v-if="groupEdit == 'image'">
                        <v-btn large @click="modalEditClose" elevation="1">
                            <?= lang('App.close'); ?>
                        </v-btn>
                    </div>
                    <div v-else>
                        <v-btn large color="primary" @click="updateSetting" :loading="loading2" elevation="1">
                            <v-icon>mdi-content-save</v-icon> <?= lang('App.save'); ?>
                        </v-btn>
                    </div>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<!-- End Modal Edit -->

<v-dialog v-model="loading2" hide-overlay persistent width="300">
    <v-card>
        <v-card-text class="pt-3">
            <?= lang('App.loadingWait'); ?>
            <v-progress-linear indeterminate color="primary" class="mb-0"></v-progress-linear>
        </v-card-text>
    </v-card>
</v-dialog>
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
        settingData: [],
        dataTable: [{
                text: '#',
                value: 'id'
            }, {
                text: 'Group',
                value: 'group_setting'
            },
            {
                text: 'Variable',
                value: 'variable_setting'
            },
            {
                text: 'Value',
                value: 'value_setting'
            },
            {
                text: 'Deskripsi',
                value: 'deskripsi_setting'
            },
            {
                text: 'Tgl Update',
                value: 'updated_at'
            },
            {
                text: 'Aksi',
                value: 'actions',
                sortable: false
            },
        ],
        settingId: "",
        groupEdit: "",
        variableEdit: "",
        deskripsiEdit: "",
        valueEdit: "",
        deskripsi_settingError: "",
        value_settingError: "",
        image: null,
        imagePreview: null,
        overlay: false,
        dataPosition: [{
            text: 'Atas (Top)',
            value: 'top'
        }, {
            text: 'Bawah (Bottom)',
            value: 'bottom'
        }],
        dataTrueFalse: [{
            text: 'True',
            value: 'true'
        }, {
            text: 'False',
            value: 'false'
        }],
        dataLeftRight: [{
            text: 'Left',
            value: 'left'
        }, {
            text: 'Right',
            value: 'right'
        }],
        dataTheme: [{
            text: 'Primary',
            value: 'primary'
        }, {
            text: 'Secondary',
            value: 'secondary'
        }, {
            text: 'Accent',
            value: 'accent'
        }, {
            text: 'Success',
            value: 'success'
        }, {
            text: 'Warning',
            value: 'warning'
        }, {
            text: 'Error',
            value: 'error'
        }, {
            text: 'Blue',
            value: 'blue'
        }, {
            text: 'Green',
            value: 'green'
        }, {
            text: 'Yellow',
            value: 'yellow'
        }, {
            text: 'Red',
            value: 'red'
        }, {
            text: 'Indigo',
            value: 'indigo'
        }, {
            text: 'Pink',
            value: 'pink'
        }, {
            text: 'Purple',
            value: 'purple'
        }, {
            text: 'Orange',
            value: 'orange'
        }, {
            text: 'Cyan',
            value: 'cyan'
        }, {
            text: 'Teal',
            value: 'teal'
        }, {
            text: 'Grey',
            value: 'grey'
        }, {
            text: 'Dark',
            value: 'dark'
        }, {
            text: 'White',
            value: 'white'
        }, {
            text: 'Black',
            value: 'black'
        }, ],
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        this.getSetting();
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
            axios.post(`<?= base_url() ?>api/setting/upload`, formData, options)
                .then(res => {
                    // handle success
                    this.loading2 = false
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.valueEdit = data.data
                        this.overlay = true;
                        this.getSetting();
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

        // Get Setting
        getSetting: function() {
            this.loading = true;
            axios.get('<?= base_url() ?>api/setting', options)
                .then(res => {
                    // handle success
                    this.loading = false;
                    var data = res.data;
                    if (data.status == true) {
                        //this.snackbar = true;
                        //this.snackbarMessage = data.message;
                        this.settingData = data.data;
                        //console.log(this.settingData);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.settingData = data.data;
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

        // Get Item Edit
        editItem: function(item) {
            this.modalEdit = true;
            this.notifType = "";
            this.settingId = item.id;
            this.groupEdit = item.group_setting;
            this.variableEdit = item.variable_setting;
            this.deskripsiEdit = item.deskripsi_setting;
            this.valueEdit = item.value_setting;
        },

        modalEditClose: function() {
            this.modalEdit = false;
            this.image = null;
            this.imagePreview = null;
            this.overlay = false;
            this.$refs.form.resetValidation();
        },

        //Update
        updateSetting: function() {
            this.loading2 = true;
            axios.put(`<?= base_url() ?>api/setting/update/${this.settingId}`, {
                    variable_setting: this.variableEdit,
                    deskripsi_setting: this.deskripsiEdit,
                    value_setting: this.valueEdit
                }, options)
                .then(res => {
                    // handle success
                    this.loading2 = false;
                    var data = res.data;
                    if (data.status == true) {
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.modalEdit = false;
                        if (data.variable == 'img_favicon' || data.variable == 'img_logo' || data.variable == 'snackbars_position') {
                            location.reload();
                        } else {
                            this.getSetting();
                        }
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
    }
</script>
<?php $this->endSection("js") ?>