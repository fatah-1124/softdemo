<?php

namespace App\Modules\Penjualan\Models;

use CodeIgniter\Model;

class PenjualanItemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'penjualan_item';
    protected $primaryKey           = 'id_itempenjualan';
    protected $useAutoIncrement     = true;
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

    public function findNota($id)
    {
        $this->select("{$this->table}.*, b.nama_barang, b.kode_barang, b.deskripsi, p.faktur, p.total");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->where("{$this->table}.id_penjualan", $id);
        //$this->orderBy("{$this->table}.id_barang", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function findNotaCetak($id)
    {
        $this->select("{$this->table}.*, b.nama_barang, b.kode_barang, b.deskripsi, p.faktur, p.total");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->where("{$this->table}.id_penjualan", $id);
        //$this->orderBy("{$this->table}.id_barang", "ASC");
        $query = $this->get()->getResult();
        return $query;
    }
}
