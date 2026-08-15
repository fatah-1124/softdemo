<?php $this->extend("layouts/frontend"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container :class="$vuetify.theme.dark ? '':'indigo lighten-5'" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm8 md4>
                <v-card>
                    <v-card-text>
                        <v-row>
                            <!-- <v-col cols="12" sm="5" style="background-image: url('<?= base_url() . $img_background; ?>') !important;background-position: center;background-repeat: no-repeat;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;">
                            </v-col> -->
                            <v-col cols="12" sm="12" class="pa-8" flex align-center justify-center>
                                <!-- <h1 class="text-h4 font-weight-medium mb-5">Sistem Kasir dan Manajemen Stok</h1> -->
                                <?php if (session()->getFlashdata('success')) { ?>
                                    <v-alert type="success" dismissible v-model="alert">
                                        <?= session()->getFlashdata('success') ?>
                                    </v-alert>
                                <?php } ?>
                                <v-form v-model="valid" ref="form">
                                    <p class="mb-2">Email</p>
                                    <v-text-field label="<?= lang('App.labelEmail') ?>" v-model="email" :rules="[rules.email]" :error-messages="emailError" outlined></v-text-field>

                                    <p class="mb-2">Password</p>
                                    <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" @click:append="show1 = !show1" :error-messages="passwordError" counter outlined></v-text-field>

                                    <v-layout justify-space-between>
                                        <!-- <v-checkbox v-model="remember" label="Remember Me" class="mt-0"></v-checkbox> -->
                                        <v-spacer></v-spacer>
                                        <a class="subtitle-2 mb-4" href="<?= base_url('password/reset') ?>"><?= lang('App.forgotPass') ?></a>
                                    </v-layout>

                                    <v-btn x-large block @click="submit" color="primary" :loading="loading">Login</v-btn>
                                </v-form>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    // Deklarasi errorKeys
    var errorKeys = []

    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
    }

    // Initial Data
    dataVue = {
        ...dataVue,
        alert: false,
        show1: false,
        email: "",
        emailError: "",
        password: "",
        passwordError: "",
        remember: true,
    }

    // Vue Created
    createdVue = function() {
        this.alert = true;
        setTimeout(() => {
            this.alert = false
        }, 5000)
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            axios.post('<?= base_url('auth/login') ?>', {
                    email: this.email,
                    password: this.password,
                })
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        localStorage.setItem('access_token', JSON.stringify(data.access_token));
                        this.snackbar = true;
                        this.snackbarMessage = data.message;
                        this.$refs.form.resetValidation();
                        setTimeout(() => window.location.reload(), 1000);
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
                    this.loading = false
                })
        },
        clear() {
            this.$refs.form.reset()
        }
    }
</script>

<?php $this->endSection("js") ?>