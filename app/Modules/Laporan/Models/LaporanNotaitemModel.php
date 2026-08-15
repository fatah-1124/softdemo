<?php

namespace App\Modules\Laporan\Models;

use CodeIgniter\Model;

class LaporanNotaitemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'penjualan_item';
    protected $primaryKey           = 'id_itempenjualan';
    protected $useAutoIncrement     = false;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function detailLaporanByKategori($start, $end, $id)
    {
        $this->select("p.faktur, p.subtotal, p.diskon, p.diskon_persen, p.total, p.pajak, p.pembulatan, {$this->table}.qty, {$this->table}.jumlah, {$this->table}.satuan, b.id_barang, b.kode_barang, b.nama_barang, {$this->table}.created_at");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        $this->where("b.id_kategori", $id);
        $this->orderBy("{$this->table}.id_itempenjualan", 'DESC');
        $query = $this->findAll();
        return $query;
    }

}
