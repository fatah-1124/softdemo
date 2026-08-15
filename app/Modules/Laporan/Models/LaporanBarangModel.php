<?php

namespace App\Modules\Laporan\Models;

use CodeIgniter\Model;

class LaporanBarangModel extends Model
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

    public function getLaporanByBarang($start, $end)
    {
        $this->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $this->select("{$this->table}.*, b.kode_barang, b.nama_barang, b.barcode, b.sku, b.stok, m.media_path, k.nama_kategori");
        $this->join("barang b", "b.id_barang = {$this->table}.id_barang");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = b.id_kategori");
        $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        //$this->groupBy("{$this->table}.id_barang");
        $this->orderBy("{$this->table}.id_itempenjualan", 'DESC');
        $query = $this->findAll();
        return $query;
    }

    public function getLaporanByStok($start, $end)
    {
        $this->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $db = \Config\Database::connect();
        $query = $db->table('barang')
            ->select("barang.*, m.media_path, k.nama_kategori, p.satuan")
            ->join("{$this->table} p", "p.id_barang = barang.id_barang", "left")
            ->join("media m", "m.id_barang = barang.id_barang", "left")
            ->join("kategori k", "k.id_kategori = barang.id_kategori")
            //->where("DATE(barang.created_at) BETWEEN '$start' AND '$end'", null, false)
            ->groupBy("barang.id_barang")
            ->orderBy("barang.id_barang", 'ASC');
        return $query->get()->getResultArray();
    }

}
