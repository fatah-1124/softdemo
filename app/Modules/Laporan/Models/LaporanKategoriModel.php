<?php

namespace App\Modules\Laporan\Models;

use CodeIgniter\Model;

class LaporanKategoriModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'kategori';
    protected $primaryKey           = 'id_kategori';
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

    public function getLaporanByKategori($start, $end)
    {
        $this->select("{$this->table}.id_kategori, {$this->table}.nama_kategori, sum(pi.qty) as qty, sum(pi.jumlah) as jumlah");
        $this->join("barang b", "b.id_kategori = {$this->table}.id_kategori");
        $this->join("penjualan_item pi", "pi.id_barang = b.id_barang");
        $this->where("DATE(pi.created_at) BETWEEN '$start' AND '$end'", null, false);
        $this->groupBy("{$this->table}.id_kategori");
        $this->orderBy("{$this->table}.id_kategori", 'DESC');
        $query = $this->findAll();
        return $query;
    }
}
