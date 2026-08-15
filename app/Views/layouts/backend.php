<?php
// Memanggil library
$language = new \App\Libraries\Language();
$permission = new \App\Libraries\Permission();
$user_permission = $permission->init();

$setting = new \App\Libraries\Settings();
$appname = $setting->info['app_name'];
$appversion = $setting->info['app_version'];
$icon = $setting->info['img_favicon'];
$logo = $setting->info['img_logo'];
$background = $setting->info['img_background'];
$snackbarsPosition = $setting->info['snackbars_position'];
$sidebarColor = $setting->info['sidebar_color'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?= env('appName') ?> Aplikasi Kasir Warung dibuat Oleh <?= env('appCompany') . ', ' . env('appAddress') ?>">
    <title><?= $title; ?> - <?= env('appName') ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="<?= base_url('assets/css/materialdesignicons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/vuetify.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.core.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.snow.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/quill.bubble.css') ?>" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- ========================= Preloader start ========================= -->
    <div class="preloader">
        <div class="loader">
            <div class="loader-logo"><img src="<?= base_url(); ?><?= $logo; ?>" alt="Preloader" width="64" style="margin-top: 5px;"></div>
            <div class="spinner">
                <div class="spinner-container">
                    <div class="spinner-rotator">
                        <div class="spinner-left">
                            <div class="spinner-circle"></div>
                        </div>
                        <div class="spinner-right">
                            <div class="spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader end -->

    <!-- Vue.js app -->
    <div id="app">
        <!-- Vuetify app -->
        <v-app>
            <!-- Topbar -->
            <v-app-bar app elevation="2">
                <v-app-bar-nav-icon @click.stop="sidebarMenu = !sidebarMenu"></v-app-bar-nav-icon>
                <v-toolbar-title></v-toolbar-title>
                <?php if (in_array('createPenjualan', $user_permission)) : ?>
                    <v-btn class="pa-2 me-2" outlined elevation="0" link href="<?= base_url('sales'); ?>">
                        <v-icon>mdi-cash-register</v-icon> <?= lang('App.sell'); ?>
                    </v-btn>
                <?php endif; ?>
                <?php if (in_array('viewPembelian', $user_permission)) : ?>
                    <v-btn class="pa-2" outlined elevation="0" link href="<?= base_url('pembelian'); ?>">
                        <v-icon>mdi-cart</v-icon> <?= lang('App.buy'); ?>
                    </v-btn>
                <?php endif; ?>
                <v-spacer></v-spacer>
                <?php if (!empty(session()->get('username'))) : ?>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn text v-bind="attrs" v-on="on">
                                <v-icon>mdi-account-circle</v-icon> <span class="d-none d-sm-flex"><?= session()->get('email') ?></span> <v-icon>mdi-chevron-down</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item class="d-flex justify-center">
                                <v-list-item-avatar size="100">
                                    <v-img src="<?= base_url('assets/images/default.png'); ?>"></v-img>
                                </v-list-item-avatar>
                            </v-list-item>
                            <v-list-item link>
                                <v-list-item-content>
                                    <v-list-item-title class="text-h6">
                                        Hai, <?= session()->get('nama') ?>
                                    </v-list-item-title>
                                    <v-list-item-subtitle><?= session()->get('email') ?></v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="<?= base_url(); ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Back to Home</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item link href="<?= base_url('logout'); ?>" @click="localStorage.removeItem('access_token')">
                                <v-list-item-icon>
                                    <v-icon>mdi-logout</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Logout</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                <?php endif; ?>
                <v-btn icon @click.stop="rightMenu = !rightMenu">
                    <v-icon>mdi-cog-outline</v-icon>
                </v-btn>
            </v-app-bar>

            <!-- Menu Navigation Drawer Kiri -->
            <v-navigation-drawer v-model="sidebarMenu" color="<?= $sidebarColor; ?>" <?= ($sidebarColor == 'white' ? 'light' : 'dark'); ?> app floating :permanent="sidebarMenu" :mini-variant.sync="mini" v-if="!isMobile">
                <v-list dense>
                    <v-list-item>
                        <v-list-item-action>
                            <v-icon @click.stop="toggleMini = !toggleMini">mdi-chevron-left</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title class="text-subtitle-1">
                                <?= env('appName') ?>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
                <v-divider></v-divider>
                <v-list>
                    <?php $uri = new \CodeIgniter\HTTP\URI(current_url()); ?>
                    <v-list-item-group>
                        <?php if (in_array('menuDashboard', $user_permission)) : ?>
                            <v-list-item link href="<?= base_url('dashboard'); ?>" <?php if ($uri->getSegment(1) == "dashboard") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.dashboard') ?>" alt="<?= lang('App.dashboard') ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.dashboard') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        <?php endif; ?>

                        <?php if (in_array('menuBarang', $user_permission)) : ?>
                            <?php if (in_array('viewBarang', $user_permission)) : ?>
                                <v-list-item link href="<?= base_url('barang'); ?>" <?php if ($uri->getSegment(1) == "barang") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.listItems') ?>" alt="<?= lang('App.listItems') ?>">
                                    <v-list-item-icon>
                                        <v-icon>mdi-package-variant-closed</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title><?= lang('App.listItems') ?></v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (in_array('menuTransaksi', $user_permission)) : ?>
                            <v-list-group color="white" prepend-icon="mdi-swap-horizontal-bold" <?php if ($uri->getSegment(1) == "sales" || $uri->getSegment(1) == "penjualan" || $uri->getSegment(1) == "pembelian"  || $uri->getSegment(1) == "hutang" || $uri->getSegment(1) == "piutang" || $uri->getSegment(1) == "biaya") : ?><?= 'value="true"'; ?><?php endif; ?> title="<?= lang('App.transaction') ?>" alt="<?= lang('App.transaction') ?>">
                                <template v-slot:activator>
                                    <v-list-item-content>
                                        <v-list-item-title><?= lang('App.transaction'); ?></v-list-item-title>
                                    </v-list-item-content>
                                </template>

                                <?php if (in_array('viewPenjualan', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('penjualan'); ?>" <?php if ($uri->getSegment(1) == "penjualan") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.sales') ?>" alt="<?= lang('App.sales') ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-receipt-text</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.sales') ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewPembelian', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('pembelian'); ?>" <?php if ($uri->getSegment(1) == "pembelian") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.purchases') ?>" alt="<?= lang('App.purchases') ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-cart</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.purchases') ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewHutang', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('hutang'); ?>" <?php if ($uri->getSegment(1) == "hutang") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.debts') ?>" alt="<?= lang('App.debts') ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-tag-arrow-right</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.debts') ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewPiutang', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('piutang'); ?>" <?php if ($uri->getSegment(1) == "piutang") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.receivables') ?>" alt="<?= lang('App.receivables') ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-book-arrow-left</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.receivables') ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewBiaya', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('biaya'); ?>" <?php if ($uri->getSegment(1) == "biaya") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.cost') ?>" alt="<?= lang('App.cost') ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-wallet</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.cost') ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                            </v-list-group>
                        <?php endif; ?>

                        <?php if (in_array('menuKeuangan', $user_permission)) : ?>
                            <v-list-group color="white" prepend-icon="mdi-cash" <?php if ($uri->getSegment(1) == "cashflow" || $uri->getSegment(1) == "pajak" || $uri->getSegment(1) == "bank") : ?><?= 'value="true"'; ?><?php endif; ?> title="<?= lang('App.finance') ?>" alt="<?= lang('App.finance') ?>">
                                <template v-slot:activator>
                                    <v-list-item-content>
                                        <v-list-item-title><?= lang('App.finance'); ?></v-list-item-title>
                                    </v-list-item-content>
                                </template>

                                <?php if (in_array('viewCashflow', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('cashflow'); ?>" <?php if ($uri->getSegment(1) == "cashflow") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.cashflow'); ?>" alt="<?= lang('App.cashflow'); ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-file-document</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.cashflow'); ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewBank', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('bank'); ?>" <?php if ($uri->getSegment(1) == "bank") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="Bank" alt="Bank">
                                        <v-list-item-icon>
                                            <v-icon>mdi-credit-card</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Bank</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewPajak', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('pajak'); ?>" <?php if ($uri->getSegment(1) == "pajak") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.tax'); ?>" alt="<?= lang('App.tax'); ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-file-percent</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.tax'); ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                            </v-list-group>
                        <?php endif; ?>

                        <?php if (in_array('menuKontak', $user_permission)) : ?>
                            <v-list-item link href="<?= base_url('kontak'); ?>" <?php if ($uri->getSegment(1) == "kontak") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.contact'); ?>" alt="<?= lang('App.contact'); ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-account-group</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.contact'); ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        <?php endif; ?>

                        <?php if (in_array('menuLaporan', $user_permission)) : ?>
                            <v-list-item link href="<?= base_url('laporan'); ?>" <?php if ($uri->getSegment(1) == "laporan") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.report') ?>" alt="<?= lang('App.report') ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-file-chart</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.report') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        <?php endif; ?>

                        <?php if (in_array('menuStatistik', $user_permission)) : ?>
                            <v-list-item link href="<?= base_url('statistik'); ?>" <?php if ($uri->getSegment(1) == "statistik") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.statistic') ?>" alt="<?= lang('App.statistic') ?>">
                                <v-list-item-icon>
                                    <v-icon>mdi-chart-bar</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title><?= lang('App.statistic') ?></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        <?php endif; ?>

                        <?php if (in_array('menuUser', $user_permission)) : ?>
                            <v-list-group color="white" prepend-icon="mdi-account-multiple" <?php if ($uri->getSegment(1) == "user" || $uri->getSegment(1) == "group") : ?><?= 'value="true"'; ?><?php endif; ?> title="<?= lang('App.users') ?>" alt="<?= lang('App.users') ?>">
                                <template v-slot:activator>
                                    <v-list-item-content>
                                        <v-list-item-title><?= lang('App.users'); ?></v-list-item-title>
                                    </v-list-item-content>
                                </template>

                                <?php if (in_array('viewUser', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('user'); ?>" <?php if ($uri->getSegment(1) == "user") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.users'); ?>" alt="<?= lang('App.users'); ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-account</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.users'); ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewGroup', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('group'); ?>" <?php if ($uri->getSegment(1) == "group") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="Group" alt="Group">
                                        <v-list-item-icon>
                                            <v-icon>mdi-shield-check</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Group</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                            </v-list-group>
                        <?php endif; ?>

                        <?php if (in_array('menuSetting', $user_permission)) : ?>
                            <v-list-group color="white" prepend-icon="mdi-store-cog" <?php if ($uri->getSegment(1) == "settings" || $uri->getSegment(1) == "toko" || $uri->getSegment(1) == "backup") : ?><?= 'value="true"'; ?><?php endif; ?> title="<?= lang('App.settings') ?>" alt="<?= lang('App.settings') ?>">
                                <template v-slot:activator>
                                    <v-list-item-content>
                                        <v-list-item-title><?= lang('App.settings'); ?></v-list-item-title>
                                    </v-list-item-content>
                                </template>

                                <?php if (in_array('viewSetting', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('settings'); ?>" <?php if ($uri->getSegment(1) == "settings") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.application'); ?>" alt="<?= lang('App.application'); ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-cog</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.application'); ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewConfig', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('toko'); ?>" <?php if ($uri->getSegment(1) == "toko") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="<?= lang('App.store'); ?>" alt="<?= lang('App.store'); ?>">
                                        <v-list-item-icon>
                                            <v-icon>mdi-store</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title><?= lang('App.store'); ?></v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>

                                <?php if (in_array('viewBackup', $user_permission)) : ?>
                                    <v-list-item link href="<?= base_url('backup'); ?>" <?php if ($uri->getSegment(1) == "backup") : ?><?= 'class="v-item--active v-list-item--active"'; ?><?php endif; ?> title="Backup Database" alt="Backup Database">
                                        <v-list-item-icon>
                                            <v-icon>mdi-database</v-icon>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>Backup DB</v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                <?php endif; ?>
                            </v-list-group>
                        <?php endif; ?>

                    </v-list-item-group>
                </v-list>

                <template v-slot:append>
                    <v-divider></v-divider>
                    <v-menu bottom min-width="200px" rounded offset-y>
                        <template v-slot:activator="{ on }">
                            <v-btn icon x-large v-on="on">
                                <v-avatar color="orange" size="36">
                                    <span class="white--text text-h6">
                                        <?= substr(session()->get('nama'), 0, 1); ?>
                                    </span>
                                </v-avatar>
                            </v-btn>
                        </template>
                        <v-card>
                            <v-list-item-content class="justify-center">
                                <div class="mx-auto text-center">
                                    <v-btn depressed rounded text>
                                        <?= session()->get('nama'); ?><br />
                                        <?= session()->get('email') ?>
                                    </v-btn>
                                    <v-divider class="my-3"></v-divider>
                                    <v-btn link href="<?= base_url('logout'); ?>" @click="localStorage.removeItem('access_token')" depressed rounded text>
                                        <v-icon>mdi-logout</v-icon> Logout
                                    </v-btn>
                                </div>
                            </v-list-item-content>
                        </v-card>
                    </v-menu>
                </template>
            </v-navigation-drawer>

            <!-- Menu Navigation Drawer Kanan -->
            <v-navigation-drawer v-model="rightMenu" app right bottom temporary>
                <template v-slot:prepend>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title>Options</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>

                <v-divider></v-divider>

                <v-list-item>
                    <v-list-item-avatar>
                        <v-icon>mdi-theme-light-dark</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        Tema {{themeText}}
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-switch v-model="dark" inset @click="toggleTheme"></v-switch>
                    </v-list-item-action>
                </v-list-item>
                <v-list-item>
                    <v-list-item-avatar>
                        <v-icon>mdi-earth</v-icon>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        Lang
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-btn-toggle>
                            <v-btn text small link href="<?= base_url('lang/id'); ?>">
                                ID
                            </v-btn>
                            <v-btn text small link href="<?= base_url('lang/en'); ?>">
                                EN
                            </v-btn>
                        </v-btn-toggle>
                    </v-list-item-action>
                </v-list-item>
            </v-navigation-drawer>

            <!-- Main Content -->
            <v-main>
                <div class="pt-4 pa-3 mb-8">
                    <?= $this->renderSection('content') ?>
                </div>
            </v-main>

            <v-container>
                <p class="text-center text-caption mb-0">
                    &copy; {{ new Date().getFullYear() }} <a href="<?= env('appWebsite'); ?>" target="_blank"><?= env('appCompany'); ?></a> <?= env('appAddress') ?>. 
                </p>
            </v-container>

            <!-- Snackbar Notification -->
            <v-snackbar v-model="snackbar" :color="snackbarType" :timeout="timeout" <?= $snackbarsPosition; ?> <?php if ($snackbarsPosition == 'top') { ?> style="top: 30px;" <?php } else { ?> style="bottom: 40px;" <?php } ?>>
                <span v-if="snackbar">{{snackbarMessage}}</span>
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="snackbar = false">
                        OK
                    </v-btn>
                </template>
            </v-snackbar>
        </v-app>
    </div>

    <!-- Scripts -->
    <script>
        var BASE_URL = '<?= base_url() ?>';
        document.addEventListener('DOMContentLoaded', init, false);

        function init() {
            if ('serviceWorker' in navigator && navigator.onLine) {
                navigator.serviceWorker.register(BASE_URL + 'service-worker.js')
                    .then((reg) => {
                        console.log('Registrasi service worker Berhasil', reg);
                    }, (err) => {
                        console.error('Registrasi service worker Gagal', err);
                    });
            }
        }
    </script>
    <script src="<?= base_url('assets/js/preloader.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuetify-image-input.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/axios.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vuejs-paginate.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/Chart.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue-chartjs.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/dayjs.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/dayjs-locale-id.js') ?>"></script>
    <script src="<?= base_url('assets/js/vue-masonry-plugin-window.js') ?>"></script>
    <script src="<?= base_url('assets/js/anime.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/html5-qrcode.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/quill.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/vue-quill-editor.js') ?>" type="text/javascript"></script>

    <script>
        dayjs.locale('id');
        dayjs().locale('id').format();
    </script>

    <script>
        var vue = null;
        var computedVue = {
            mini: {
                get() {
                    return this.$vuetify.breakpoint.xsOnly || !this.toggleMini;
                },
                set(value) {
                    this.toggleMini = value;
                }
            },
            isMobile() {
                if (this.$vuetify.breakpoint.xsOnly) {
                    return this.sidebarMenu = false
                }
            },
            themeText() {
                return this.$vuetify.theme.dark ? '<?= lang('App.dark') ?>' : '<?= lang('App.light') ?>'
            }
        }
        var createdVue = function() {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
        var mountedVue = function() {
            const theme = localStorage.getItem("dark_theme");
            if (theme) {
                if (theme === "true") {
                    this.$vuetify.theme.dark = true;
                    this.dark = true;
                } else {
                    this.$vuetify.theme.dark = false;
                    this.dark = false;
                }
            } else if (
                window.matchMedia &&
                window.matchMedia("(prefers-color-scheme: dark)").matches
            ) {
                this.$vuetify.theme.dark = false;
                localStorage.setItem(
                    "dark_theme",
                    this.$vuetify.theme.dark.toString()
                );
            }
        }
        var updatedVue = function() {}
        var watchVue = {}
        var dataVue = {
            sidebarMenu: true,
            rightMenu: false,
            toggleMini: false,
            dark: false,
            group: null,
            search: '',
            loading: false,
            loading1: false,
            loading2: false,
            loading3: false,
            loading4: false,
            loading5: false,
            loading6: false,
            loading7: false,
            loading8: false,
            loading9: false,
            loading10: false,
            valid: true,
            notifMessage: '',
            notifType: '',
            snackbar: false,
            timeout: 4000,
            snackbarType: '',
            snackbarMessage: '',
            show: false,
            show1: false,
            show2: false,
            rules: {
                email: v => !!(v || '').match(/@/) || '<?= lang('App.emailValid'); ?>',
                length: len => v => (v || '').length <= len || `<?= lang('App.invalidLength'); ?> ${len}`,
                password: v => !!(v || '').match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/) ||
                    '<?= lang('App.strongPassword'); ?>',
                min: v => v.length >= 8 || '<?= lang('App.minChar'); ?>',
                required: v => !!v || '<?= lang('App.isRequired'); ?>',
                number: v => Number.isInteger(Number(v)) || "<?= lang('App.isNumber'); ?>",
                decimal: v => Number.isFinite(Number(v)) || "<?= lang('App.isNumber'); ?>",
                zero: v => v > 0 || "<?= lang('App.isZero'); ?>"
            },
            editorOption: {
                theme: 'snow',
                modules: {
                    'toolbar': [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        [{
                            'font': []
                        }],
                        ['bold', 'italic', 'underline'], // toggled buttons
                        ['blockquote', 'code-block'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'align': []
                        }],
                        ['link', 'image'],
                        ['clean']
                    ],
                },
            },
        }
        var methodsVue = {
            toggleTheme() {
                this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
                localStorage.setItem("dark_theme", this.$vuetify.theme.dark.toString());
            },
            formatNumber(number) {
                const formattedNumber = new Intl.NumberFormat('<?= $language->siteLang; ?>', {
                    notation: 'compact',
                    compactDisplay: 'short',
                }).format(number);
                return formattedNumber;
            },
        }
        Vue.component('paginate', VuejsPaginate);
        var VueMasonryPlugin = window["vue-masonry-plugin"].VueMasonryPlugin;
        Vue.use(VueMasonryPlugin);
        Vue.component('qrcode-scanner', {
            template: `<div id="reader"></div>`,
            mounted() {
                const html5QrCode = new Html5Qrcode("reader");
                const config = {
                    fps: 60,
                    aspectRatio: 1.0,
                    qrbox: {
                        width: 240,
                        height: 200
                    },
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };
                html5QrCode.start({
                    facingMode: "environment"
                }, config, this.onScanSuccess).catch(err => {
                    alert(`Error scanning. Reason: ${err}`);
                    console.log(`Error scanning. Reason: ${err}`)
                });
            },
            methods: {
                onScanSuccess(decodedText, decodedResult) {
                    this.$emit('result', decodedText, decodedResult);
                    html5QrCode.stop();
                },
            }
        });
        Vue.use(VueQuillEditor);
    </script>

    <!-- Render Script yang ada di masing-masing page -->
    <?= $this->renderSection('js') ?>

    <!-- Vue.js dan Vuetify.js -->
    <script>
        new Vue({
            el: '#app',
            vuetify: new Vuetify(),
            computed: computedVue,
            data: dataVue,
            mounted: mountedVue,
            created: createdVue,
            updated: updatedVue,
            watch: watchVue,
            methods: methodsVue
        })
    </script>
</body>

</html>