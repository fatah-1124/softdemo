<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email'     => 'admin@gmail.com',
            'username'  => 'admin',
            'password'  => '12345678',
            'role'      => 1,
            'active'    => 1,
            'token'     => null
        ];
        
        $model = new UserModel();
        $model->save($data);
    }
}
