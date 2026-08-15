<?php $this->extend("layouts/backend"); ?>
<?php $this->section("content"); ?>
<template>
    <h1 class="font-weight-medium mb-2"><?= $title ?></span></h1>
    <v-card>
        <v-card-title>

        </v-card-title>
        <v-card-text>
            <?= form_open('/group/update/' . $id) ?>

            <v-text-field type="text" name="nama_group" value="<?= $group['nama_group']; ?>" label="Nama Group" outlined></v-text-field>

            <div class="v-data-table theme--light">
                <div class="v-data-table__wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th width="100"></th>
                                <th class="text-left" width="100">View</th>
                                <th class="text-left" width="100">Create</th>
                                <th class="text-left" width="100">Update</th>
                                <th class="text-left" width="100">Delete</th>
                                <th class="text-left" width="100">Menu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-h6">Dashboard</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewDashboard" value="viewDashboard" <?php if ($permissions) { ?> <?php if (in_array('viewDashboard', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewDashboard"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuDashboard" value="menuDashboard" <?php if ($permissions) { ?><?php if (in_array('menuDashboard', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuDashboard"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">BARANG</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuBarang" value="menuBarang" <?php if ($permissions) { ?><?php if (in_array('menuBarang', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuBarang"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Barang</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewBarang" value="viewBarang" <?php if ($permissions) { ?> <?php if (in_array('viewBarang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewBarang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createBarang" value="createBarang" <?php if ($permissions) { ?> <?php if (in_array('createBarang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createBarang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateBarang" value="updateBarang" <?php if ($permissions) { ?> <?php if (in_array('updateBarang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateBarang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteBarang" value="deleteBarang" <?php if ($permissions) { ?><?php if (in_array('deleteBarang', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteBarang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Barang Import/Export</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewExcel" value="viewExcel" <?php if ($permissions) { ?> <?php if (in_array('viewExcel', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewExcel"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createExcel" value="createExcel" <?php if ($permissions) { ?> <?php if (in_array('createExcel', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createExcel"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">TRANSAKSI</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuTransaksi" value="menuTransaksi" <?php if ($permissions) { ?><?php if (in_array('menuTransaksi', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuTransaksi"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Penjualan</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewPenjualan" value="viewPenjualan" <?php if ($permissions) { ?> <?php if (in_array('viewPenjualan', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewPenjualan"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createPenjualan" value="createPenjualan" <?php if ($permissions) { ?> <?php if (in_array('createPenjualan', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createPenjualan"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updatePenjualan" value="updatePenjualan" <?php if ($permissions) { ?> <?php if (in_array('updatePenjualan', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updatePenjualan"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deletePenjualan" value="deletePenjualan" <?php if ($permissions) { ?><?php if (in_array('deletePenjualan', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deletePenjualan"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Pembelian</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewPembelian" value="viewPembelian" <?php if ($permissions) { ?> <?php if (in_array('viewPembelian', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewPembelian"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createPembelian" value="createPembelian" <?php if ($permissions) { ?> <?php if (in_array('createPembelian', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createPembelian"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updatePembelian" value="updatePembelian" <?php if ($permissions) { ?> <?php if (in_array('updatePembelian', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updatePembelian"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deletePembelian" value="deletePembelian" <?php if ($permissions) { ?><?php if (in_array('deletePembelian', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deletePembelian"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Hutang</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewHutang" value="viewHutang" <?php if ($permissions) { ?> <?php if (in_array('viewHutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewHutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createHutang" value="createHutang" <?php if ($permissions) { ?> <?php if (in_array('createHutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createHutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateHutang" value="updateHutang" <?php if ($permissions) { ?> <?php if (in_array('updateHutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateHutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteHutang" value="deleteHutang" <?php if ($permissions) { ?><?php if (in_array('deleteHutang', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteHutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Piutang</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewPiutang" value="viewPiutang" <?php if ($permissions) { ?> <?php if (in_array('viewPiutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewPiutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createPiutang" value="createPiutang" <?php if ($permissions) { ?> <?php if (in_array('createPiutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createPiutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updatePiutang" value="updatePiutang" <?php if ($permissions) { ?> <?php if (in_array('updatePiutang', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updatePiutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deletePiutang" value="deletePiutang" <?php if ($permissions) { ?><?php if (in_array('deletePiutang', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deletePiutang"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Biaya</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewBiaya" value="viewBiaya" <?php if ($permissions) { ?> <?php if (in_array('viewBiaya', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewBiaya"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createBiaya" value="createBiaya" <?php if ($permissions) { ?> <?php if (in_array('createBiaya', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createBiaya"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateBiaya" value="updateBiaya" <?php if ($permissions) { ?> <?php if (in_array('updateBiaya', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateBiaya"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteBiaya" value="deleteBiaya" <?php if ($permissions) { ?><?php if (in_array('deleteBiaya', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteBiaya"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">KEUANGAN</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuKeuangan" value="menuKeuangan" <?php if ($permissions) { ?><?php if (in_array('menuKeuangan', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuKeuangan"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Cashflow</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewCashflow" value="viewCashflow" <?php if ($permissions) { ?> <?php if (in_array('viewCashflow', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewCashflow"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createCashflow" value="createCashflow" <?php if ($permissions) { ?> <?php if (in_array('createCashflow', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createCashflow"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateCashflow" value="updateCashflow" <?php if ($permissions) { ?> <?php if (in_array('updateCashflow', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateCashflow"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteCashflow" value="deleteCashflow" <?php if ($permissions) { ?><?php if (in_array('deleteCashflow', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteCashflow"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Pajak</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewPajak" value="viewPajak" <?php if ($permissions) { ?> <?php if (in_array('viewPajak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewPajak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createPajak" value="createPajak" <?php if ($permissions) { ?> <?php if (in_array('createPajak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createPajak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updatePajak" value="updatePajak" <?php if ($permissions) { ?> <?php if (in_array('updatePajak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updatePajak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deletePajak" value="deletePajak" <?php if ($permissions) { ?><?php if (in_array('deletePajak', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deletePajak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">KONTAK</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuKontak" value="menuKontak" <?php if ($permissions) { ?><?php if (in_array('menuKontak', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuKontak"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Kontak</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewKontak" value="viewKontak" <?php if ($permissions) { ?> <?php if (in_array('viewKontak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewKontak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createKontak" value="createKontak" <?php if ($permissions) { ?> <?php if (in_array('createKontak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createKontak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateKontak" value="updateKontak" <?php if ($permissions) { ?> <?php if (in_array('updateKontak', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateKontak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteKontak" value="deleteKontak" <?php if ($permissions) { ?><?php if (in_array('deleteKontak', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteKontak"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5"><?= strtoupper(lang('App.users')); ?> SISTEM</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuUser" value="menuUser" <?php if ($permissions) { ?><?php if (in_array('menuUser', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuUser"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Users</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewUser" value="viewUser" <?php if ($permissions) { ?> <?php if (in_array('viewUser', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewUser"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createUser" value="createUser" <?php if ($permissions) { ?> <?php if (in_array('createUser', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createUser"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateUser" value="updateUser" <?php if ($permissions) { ?> <?php if (in_array('updateUser', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateUser"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteUser" value="deleteUser" <?php if ($permissions) { ?><?php if (in_array('deleteUser', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteUser"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Group</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewGroup" value="viewGroup" <?php if ($permissions) { ?> <?php if (in_array('viewGroup', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewGroup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createGroup" value="createGroup" <?php if ($permissions) { ?> <?php if (in_array('createGroup', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createGroup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateGroup" value="updateGroup" <?php if ($permissions) { ?> <?php if (in_array('updateGroup', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateGroup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteGroup" value="deleteGroup" <?php if ($permissions) { ?><?php if (in_array('deleteGroup', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteGroup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">PENGATURAN</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuSetting" value="menuSetting" <?php if ($permissions) { ?><?php if (in_array('menuSetting', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="menuSetting"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Settings</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewSetting" value="viewSetting" <?php if ($permissions) { ?> <?php if (in_array('viewSetting', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewSetting"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateSetting" value="updateSetting" <?php if ($permissions) { ?> <?php if (in_array('updateSetting', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateSetting"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Atur Toko/Warung</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewConfig" value="viewConfig" <?php if ($permissions) { ?> <?php if (in_array('viewConfig', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewConfig"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateConfig" value="updateConfig" <?php if ($permissions) { ?> <?php if (in_array('updateConfig', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateConfig"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Backup DB</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewBackup" value="viewBackup" <?php if ($permissions) { ?> <?php if (in_array('viewBackup', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewBackup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="createBackup" value="createBackup" <?php if ($permissions) { ?> <?php if (in_array('createBackup', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="createBackup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteBackup" value="deleteBackup" <?php if ($permissions) { ?><?php if (in_array('deleteBackup', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteBackup"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td colspan="6">LAPORAN &amp; STATISTIK</td>
                            </tr>

                            <tr>
                                <td class="text-h6">Laporan</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewLaporan" value="viewLaporan" <?php if ($permissions) { ?> <?php if (in_array('viewLaporan', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewLaporan"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuLaporan" value="menuLaporan" <?php if ($permissions) { ?> <?php if (in_array('menuLaporan', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="menuLaporan"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6 pl-8">Laporan Laba/Rugi</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewLaporanLabaRugi" value="viewLaporanLabaRugi" <?php if ($permissions) { ?> <?php if (in_array('viewLaporanLabaRugi', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewLaporanLabaRugi"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>

                            <tr>
                                <td class="text-h6">Statistik</td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="viewStatistik" value="viewStatistik" <?php if ($permissions) { ?> <?php if (in_array('viewStatistik', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="viewStatistik"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="menuStatistik" value="menuStatistik" <?php if ($permissions) { ?> <?php if (in_array('menuStatistik', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="menuStatistik"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5">CASHIER</td>
                            </tr>

                            <tr>
                                <td class="text-h6">Shift Open/Close</td>
                                <td>
                                    -
                                </td>
                                <td>
                                    -
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="updateShift" value="updateShift" <?php if ($permissions) { ?> <?php if (in_array('updateShift', $permissions)) { ?> <?= "checked"; ?> <?php } ?> <?php } ?>>
                                            <label for="updateShift"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <input class="v-input--selection-controls__input" type="checkbox" name="permission[]" id="deleteShift" value="deleteShift" <?php if ($permissions) { ?><?php if (in_array('deleteShift', $permissions)) { ?><?= "checked"; ?><?php } ?><?php } ?>><label for="deleteShift"></label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    -
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br />
            <button type="submit" class="v-btn v-btn--is-elevated v-btn--has-bg theme--light elevation-2 v-size--large primary">Update</button>
            <?= form_close(); ?>
        </v-card-text>
    </v-card>
</template>

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
        
    }

    // Vue Created
    // Created: Dipanggil secara sinkron setelah instance dibuat
    createdVue = function() {
        
    }

    watchVue = {
        ...watchVue,
        
    }

    // Vue Methods
    // Methods: Metode-metode yang kemudian digabung ke dalam Vue instance
    methodsVue = {
        ...methodsVue,
        

    }
</script>
<?php $this->endSection("js") ?>