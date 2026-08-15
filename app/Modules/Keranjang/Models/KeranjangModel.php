<?php

namespace App\Modules\Keranjang\Models;

use CodeIgniter\Model;

class KeranjangModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'keranjang';
    protected $primaryKey           = 'id_keranjang';
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

    // Get Keranjang Jual
    public function getKeranjang()
    {
        $this->select("{$this->table}.*, b.kode_barang, b.barcode, b.nama_barang, b.harga_jual AS harga_jual_barang");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->where("{$this->table}.id_login", session()->get('id'));
        $this->orderBy("{$this->table}.id_keranjang", "ASC");
        $query = $this->findAll();
        return $query;
    }
}
