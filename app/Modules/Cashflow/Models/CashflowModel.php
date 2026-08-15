<?php

namespace App\Modules\Cashflow\Models;

use CodeIgniter\Model;

class CashflowModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'cashflow';
    protected $primaryKey           = 'id_cashflow';
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

    public function getCashflow($start = false, $end = false)
    {
        $this->select("{$this->table}.*, l.nama");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.tanggal) BETWEEN '$start' AND '$end'", null, false);
        endif;
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll();
        return $query;
    }

    public function showCashflow($id)
    {
        $this->select("{$this->table}.*, l.nama");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        $this->where("{$this->table}.id_cashflow", $id);
        $query = $this->first();
        return $query;
    }

    public function getSaldo()
    {
        $this->select("(sum(pemasukan)-sum(pengeluaran)) as total");
        return $this->get()->getRow()->total;
    }
}
