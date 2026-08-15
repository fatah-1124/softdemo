<?php

namespace App\Modules\Laporan\Models;

use CodeIgniter\Model;

class LaporanPenjualanModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'penjualan';
    protected $primaryKey           = 'id_penjualan';
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

    public function getLaporanByPenjualan($start, $end)
    {
        $this->select("{$this->table}.*, l.nama, p.id_piutang, p.sisa_piutang, p.status_piutang, k.nama as nama_kontak, k.grup");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        $this->join("piutang p", "p.id_penjualan = {$this->table}.id_penjualan", "left");
        $this->join("kontak k", "k.id_kontak = {$this->table}.id_kontak", "left");
        $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        $this->orderBy("{$this->table}.id_penjualan", 'DESC');
        $query = $this->findAll();
        return $query;
    }

    public function sumHPP($start, $end)
    {
        $this->select("sum({$this->table}.hpp) as total");
        $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        return $this->get()->getRow()->total;
    }
}
