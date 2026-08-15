<?php

namespace App\Modules\Barang\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'barang';
    protected $primaryKey           = 'id_barang';
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

    public function getBarang($where = false)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        if ($where != '') :
            $array = explode(",", $where);
            $this->whereIn("{$this->table}.id_kategori", $array);
        endif;
        $this->orderBy("{$this->table}.updated_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function showBarang($id)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        $this->where("{$this->table}.id_barang", $id);
        $this->orWhere("{$this->table}.kode_barang", $id);
        $query = $this->first();
        return $query;
    }

    public function countBarang($where = false, $condition = false)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        if ($where) {
            $this->where("{$this->table}.$where", $condition);
        }
        $query = $this->countAllResults();
        return $query;
    }

    public function getBarangTerbaru($page = false, $limit = false)
    {
        $offset = ($page - 1) * $limit;
        $this->select("{$this->table}.*, m.media_path");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->orderBy("{$this->table}.created_at", "DESC");
        return $this->findAll($limit, $offset);
    }

    public function getBarangKasir()
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        $this->where("{$this->table}.active", 1);
        $this->orderBy("{$this->table}.created_at", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function searchBarang($keyword = false)
    {
        $this->select("{$this->table}.*, k.id_kategori, k.nama_kategori");
        $this->join("kategori k", "{$this->table}.id_kategori = k.id_kategori", 'inner');
        $this->where("{$this->table}.barcode", $keyword);
        $this->orLike("{$this->table}.sku", $keyword);
        $this->orLike("{$this->table}.nama_barang", $keyword);
        $this->orLike("{$this->table}.kode_barang", $keyword);
        return $this->findAll();
    }

    public function scanBarang($keyword = false)
    {
        $this->select("{$this->table}.*, k.id_kategori, k.nama_kategori");
        $this->join("kategori k", "{$this->table}.id_kategori = k.id_kategori", 'inner');
        $this->groupStart();
        $this->like("{$this->table}.barcode", $keyword);
        $this->orLike("{$this->table}.sku", $keyword);
        $this->orLike("{$this->table}.kode_barang", $keyword);
        $this->orHaving("{$this->table}.stok >=", 1);
        $this->groupEnd();
        $this->where("{$this->table}.active", 1);
        return $this->findAll();
    }

    public function getBarangHabis($where = false)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        if ($where != '') :
            $array = explode(",", $where);
            $this->whereIn("{$this->table}.id_kategori", $array);
        endif;
        $this->where("{$this->table}.stok", 0);
        $this->orderBy("{$this->table}.updated_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function getBarangNonaktif($where = false)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        if ($where != '') :
            $array = explode(",", $where);
            $this->whereIn("{$this->table}.id_kategori", $array);
        endif;
        $this->where("{$this->table}.active", 0);
        $this->orderBy("{$this->table}.updated_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function beliBarangVendor($id)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "k.id_kategori = {$this->table}.id_kategori");
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        if ($id == '0') {
            $this->where("{$this->table}.id_kontak IS NULL",NULL,FALSE);
        } else {
            $this->where("{$this->table}.id_kontak", $id);
        }
        $this->orderBy("{$this->table}.created_at", "ASC");
        $query = $this->findAll();
        return $query;
    }

    public function findBarang($keyword = false)
    {
        $this->select("{$this->table}.*, m.media_path, k.nama_kategori, ko.nama as vendor_supplier, ko.perusahaan");
        $this->join("media m", "m.id_barang = {$this->table}.id_barang", "left");
        $this->join("kategori k", "{$this->table}.id_kategori = k.id_kategori", 'inner');
        $this->join("kontak ko", "ko.id_kontak = {$this->table}.id_kontak", "left");
        $this->groupStart();
        $this->like("{$this->table}.barcode", $keyword);
        $this->orLike("{$this->table}.nama_barang", $keyword);
        $this->orLike("{$this->table}.sku", $keyword);
        $this->orLike("{$this->table}.kode_barang", $keyword);
        $this->groupEnd();
        return $this->first();
    }
}
