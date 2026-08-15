<?php

namespace App\Modules\Hutang\Models;

use CodeIgniter\Model;

class HutangModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'hutang';
    protected $primaryKey           = 'id_hutang';
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

    public function getHutang($start = false, $end = false, $where = false)
    {
        $this->select("{$this->table}.*, p.faktur, k.nama as nama_kontak, k.perusahaan, l.nama");
        $this->join("pembelian p", "p.id_pembelian = {$this->table}.id_pembelian");
        $this->join("kontak k", "k.id_kontak = p.id_kontak", "left");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.tanggal) BETWEEN '$start' AND '$end'", null, false);
        endif;
        if ($where != '') :
            $this->where("{$this->table}.status_hutang", $where);
            $multiple = explode(",", $where);
            if (count($multiple) > 1) {
                $this->where("{$this->table}.status_hutang", $multiple[0]);
                $this->orWhere("{$this->table}.status_hutang", $multiple[1]);
            }
        endif;
        $query = $this->findAll();
        return $query;
    }
}
