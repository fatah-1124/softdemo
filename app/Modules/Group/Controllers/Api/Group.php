<?php

namespace App\Modules\Group\Controllers\Api;
/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (https://Tokopedia.com/itshoppwt, https://Shopee.co.id/itshoppwt, https://Toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
04-2023
*/

use App\Controllers\BaseControllerApi;
use App\Modules\Group\Models\GroupModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\I18n\Time;

class Group extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = GroupModel::class;
    protected $log;

    public function __construct()
    {
        //memanggil Model
        $this->log = new LogModel();
    }

    public function index()
    {
        $data = $this->model->findAll();
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function show($id = null)
    {
        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $this->model->find($id)], 200);
    }

    public function create()
    {
        $rules = [
            'nama_group' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $namaGroup = $json->nama_group;
            $data = [
                'nama_group' => $namaGroup,
            ];
        } else {
            $namaGroup = $this->request->getPost('nama_group');
            $data = [
                'nama_group' => $namaGroup,
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->save($data);
            $lastId = $this->model->getInsertID();

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Group: ' . $namaGroup]);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => ['url' => base_url('/group/edit/') . $lastId],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);

        // Default ID 1 jangan dihapus
        if ($id == '1') :
            $response = ['status' => false, 'message' => lang('App.delFailed'), 'data' => []];
            return $this->respond($response, 200);
        endif;
        //

        if ($hapus) {
            $this->model->delete($id);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Group: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

}