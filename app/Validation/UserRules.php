<?php

namespace App\Validation;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Modules\Auth\Models\LoginModel;
use Exception;

class UserRules
{
    public function validateUser(string $str, string $fields, array $data): bool
    {
        try {
            $model = new LoginModel();
            $user = $model->findUserByEmailAddress($data['email']);
            return password_verify($data['password'], $user['password']);
        } catch (Exception $e) {
            return false;
        }
    }
}
