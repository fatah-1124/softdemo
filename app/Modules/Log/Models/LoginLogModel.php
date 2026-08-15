<?php

namespace App\Modules\Log\Models;

use CodeIgniter\Model;

class LoginLogModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'login_log';
    protected $primaryKey           = 'id_log_login';
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

    public function getLoginLog($id = false, $limit = false)
    {
        $this->select("{$this->table}.*, login.nama, login.role");
        $this->join("login", "login.email = {$this->table}.email");
        if ($id) {
            $this->where("{$this->table}.email", $id);
        }
        $this->orderBy("{$this->table}.created_at", "DESC");
        $query = $this->findAll($limit);
        return $query;
    }
}
