<?php

namespace App\Modules\Group\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'groups';
    protected $primaryKey           = 'id_group';
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

    public function getGroupById($user_id) 
	{
        $this->select("{$this->table}.*, login_group.id_login_group, login_group.id_login");
        $this->join("login_group", "login_group.id_group = {$this->table}.id_group", "left");
        $this->where("login_group.id_login", $user_id);
        return $this->get()->getRowArray();
	}
}
