<?php

namespace App\Modules\Pembelian\Models;

use CodeIgniter\Model;

class PembelianItemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'pembelian_item';
    protected $primaryKey           = 'id_itempembelian';
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

    public function findItem($id)
    {
        $this->select("{$this->table}.*, b.nama_barang, p.faktur, p.total");
        $this->join("pembelian p", "p.id_pembelian = {$this->table}.id_pembelian");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->where("{$this->table}.id_pembelian", $id);
        $query = $this->findAll();
        return $query;
    }
}
