<?php

namespace App\Modules\Pembelian\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'pembelian';
    protected $primaryKey           = 'id_pembelian';
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

    public function getPembelian($start = false, $end = false)
    {
        $this->select("{$this->table}.*, l.nama");
        $this->join("login l", "l.id_login = {$this->table}.id_login");
        if ($start != "" && $end != "") :
            $this->where("DATE({$this->table}.created_at) BETWEEN '$start' AND '$end'", null, false);
        endif;
        $this->orderBy("{$this->table}.created_at", 'DESC');
        $query = $this->findAll();
        return $query;
    }

    public function chartTransaksi($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function chartHarian($date)
    {
        $this->like('created_at', $date, 'after');
        return count($this->get()->getResultArray());
    }

    public function chartPemasukan($date)
    {
        $this->select('sum(total) as total');
        $this->like('created_at', $date, 'after');
        return $this->get()->getRow()->total;
    }
}
