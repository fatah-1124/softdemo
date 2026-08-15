<?php

namespace App\Modules\Piutang\Models;

use CodeIgniter\Model;

class PiutangModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'piutang';
    protected $primaryKey           = 'id_piutang';
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

    public function getPiutang($start = false, $end = false, $where = false)
    {
        $this->select("{$this->table}.*, p.faktur, k.nama as nama_kontak, l.nama");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->join("kontak k", "k.id_kontak = p.id_kontak");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.tanggal) BETWEEN '$start' AND '$end'", null, false);
        endif;
        if ($where != '') :
            $this->where("{$this->table}.status_piutang", $where);
            $multiple = explode(",", $where);
            if (count($multiple) > 1) {
                $this->where("{$this->table}.status_piutang", $multiple[0]);
                $this->orWhere("{$this->table}.status_piutang", $multiple[1]);
            }
        endif;
        $query = $this->findAll();
        return $query;
    }

    public function getPiutangByKontak($id)
    {
        $this->select("{$this->table}.*, p.faktur, p.id_kontak, k.nama as nama_kontak, k.telepon, l.nama");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->join("kontak k", "k.id_kontak = p.id_kontak");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        $this->where("p.id_kontak", $id);
        $this->where("{$this->table}.status_piutang", 0);
        $query = $this->findAll();
        return $query;
    }

    public function totalPiutangByKontak($id)
    {
        $this->select("sum({$this->table}.sisa_piutang) as total_piutang");
        $this->join("penjualan p", "p.id_penjualan = {$this->table}.id_penjualan");
        $this->where("p.id_kontak", $id);
        $this->where("{$this->table}.status_piutang", 0);
        $this->groupBy("p.id_kontak");
        $query = $this->get()->getRow()->total_piutang ?? "";
        return $query;
    }
}
