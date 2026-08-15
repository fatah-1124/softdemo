<?php

namespace App\Modules\Media\Controllers\Api;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseControllerApi;
use App\Modules\Media\Models\MediaModel;

class Media extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = MediaModel::class;

    public function getMedia($id = null)
    {
        $data = $this->model->where(['id_barang' => $id, 'active' => 1])->first();
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

    public function create()
    {
        //Maksimal ukuran 51200 KB = 50 MB
        $rules = [
            'image' => [
                'rules'  => 'uploaded[image]|max_size[image,51200]|is_image[image]',
                'errors' => []
            ],
        ];
        $gambar = $this->request->getFile('image');
        $id_barang = $this->request->getPost('id_barang');
        // buat nama file random
        $fileName = $gambar->getRandomName();
        if (! $this->validateData([], $rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.imgFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $path = "images/";
            // pindahkan ke lokasi $path
            $moved = $gambar->move($path, $fileName);
            if ($moved) {
                $save = $this->model->save([
                    'media_path' => $path . $fileName,
                    'id_barang' => $id_barang,
                    'active' => 1
                ]);
                if ($save) {
                    return $this->respond(["status" => true, "message" => lang('App.imgSuccess'), "data" => $this->model->getInsertID()], 200);
                } else {
                    return $this->respond(["status" => false, "message" => lang('App.imgFailed'), "data" => []], 200);
                }
            }
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        if ($hapus) {
            $this->model->delete($id);
            // hapus file
            unlink($hapus['media_path']);

            $response = [
                'status' => true,
                'message' => lang('App.imgDeleted'),
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
