<?php

namespace App\Modules\Kontak\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;

class Kontak extends BaseController
{
    public function __construct()
    {
        
    }

    public function index()
    {
        return view('App\Modules\Kontak\Views/kontak', [
            'title' => lang('App.contact'),
        ]);
    }

    
}
