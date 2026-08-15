<?php $this->extend("layouts/frontend"); ?>
<?php $this->section("content"); ?>
<template>
    <v-container class="px-4 py-0 mt-n5" :class="$vuetify.theme.dark ? '':'indigo lighten-5'" fill-height fluid>
        <v-layout flex align-center justify-center>
            <v-flex xs12 sm6 md6>
                <v-card>
                    <v-card-text class="pa-10">
                        <h1 class="font-weight-normal text-center mb-10"><?= lang('App.newPassword') ?></h1>
                        <v-alert v-if="notifType != ''" dense :type="notifType">{{notifMessage}}</v-alert>
                        <v-form v-model="valid" ref="form">
                            <v-text-field v-model="password" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.required, rules.min]" :type="show1 ? 'text' : 'password'" label="Password" hint="<?= lang('App.minChar') ?>" counter @click:append="show1 = !show1" outlined dense :disabled="submitted"></v-text-field>
                            <v-text-field block v-model="verify" :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :rules="[rules.required, passwordMatch]" :type="show1 ? 'text' : 'password'" label="Confirm Password" counter @click:append="show1 = !show1" outlined dense :disabled="submitted"></v-text-field>
                            <v-layout class="mb-3">
                                <v-btn large block color="primary" @click="submit" :loading="loading" :disabled="submitted">Submit</v-btn>
                            </v-layout>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<?php $this->endSection("content") ?>

<?php $this->section("js") ?>
<script>
    // Vue Computed
    // Computed: Properti-properti terolah (computed) yang kemudian digabung kedalam Vue instance
    computedVue = {
        ...computedVue,
        passwordMatch() {
            return () => this.password === this.verify || "<?= lang('App.samePassword') ?>";
        }
    }

    // Initial Data
    dataVue = {
        ...dataVue,
        show1: false,
        submitted: false,
        email: '<?= $email ?>',
        token: '<?= $token ?>',
        password: '',
        verify: '',
        rules: {
            required: value => !!value || '<?= lang('App.required') ?>',
            min: v => v.length >= 8 || '<?= lang('App.minChar') ?>',
        },
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        submit() {
            this.loading = true;
            var formData = new FormData();
            formData.append("email", this.email)
            formData.append("token", this.token)
            formData.append("password", this.password)
            formData.append("verify", this.verify)
            axios.post('<?= base_url('auth/changePassword') ?>', {
                    formData
                })
                .then(res => {
                    // handle success
                    this.loading = false
                    var data = res.data;
                    if (data.status == true) {
                        this.submitted = true;
                        this.notifMessage = data.message;
                        this.$refs.form.resetValidation();
                        setTimeout(() => window.location.href = data.data.url, 2000);
                    } else {
                        this.snackbar = true;
                        this.snackbarMessage = data.message || data.message.password || data.message.verify;
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