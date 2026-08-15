<?php

namespace App\Modules\Pajak\Models;

use CodeIgniter\Model;

class PajakModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'pajak';
    protected $primaryKey           = 'id_pajak';
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

    public function getPajak($start = false, $end = false)
    {
        $this->select("{$this->table}.*, p.faktur as faktur_penjualan, l.nama, t.nama_toko");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan", "left");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        $this->join("toko t", "t.id_toko = {$this->table}.id_toko");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        endif;
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function showPajak($id)
    {
        $this->select("{$this->table}.*, p.faktur as faktur_penjualan, l.nama");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan", "left");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        $this->where("{$this->table}.id_pajak", $id);
        $query = $this->first();
        return $query;
    }

}
