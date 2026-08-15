<?php

namespace App\Filters;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use App\Libraries\Permission;

class PermissionBased implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $permission = new Permission();
        $cocok = 0;
        foreach ($arguments as $key => $value) {
            if (in_array($value, $permission->init())) {
                $cocok += 1;
            }
        }
       
        if ($cocok == 0) {
            $getHeader = Services::request()->getHeaderLine('accept');
            if ($getHeader == 'application/json, text/plain, */*') {
                return Services::response()->setJSON([
                    'status' => false,
                    'message' => lang('App.reqFailed'),
                    'data' => []
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return redirect('restricted', 'refresh');
            }
        }
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
