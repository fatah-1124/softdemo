<?php

namespace App\Modules\Setting\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Setting\Models\SettingModel;
use App\Modules\Log\Models\LogModel;
use Exception;

class Setting extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = SettingModel::class;
    protected $log;

    public function __construct()
    {
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

    public function update($id = NULL)
    {
        $rules = [
            'value_setting' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $variable = $json->variable_setting;
            $data = [
                'value_setting' => $json->value_setting,
            ];
        } else {
            $input = $this->request->getRawInput();
            $variable = $input['variable_setting'];
            $data = [
                'value_setting' => $input['value_setting'],
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Setting: ' . $variable]);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
                'variable' => $variable
            ];
            return $this->respond($response, 200);
        }
    }

    public function upload()
    {
        //Maksimal ukuran 51200 KB = 50 MB
        $rules = [
            'image' => [
                'rules'  => 'uploaded[image]|max_size[image,51200]|is_image[image]',
                'errors' => []
            ],
        ];
        $id = $this->request->getVar('id');
        $image = $this->request->getFile('image');
        $fileName = $image->getRandomName();
        if (! $this->validateData([], $rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.imgFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $path = "assets/images/";
            $moved = $image->move($path, $fileName);

            $image = \Config\Services::image()
                ->withFile($path . $fileName)
                ->resize(80, 80, true, 'height')
                ->save($path . 'res_' . $fileName);

            if ($moved) {
                //Simpan ke img_logo
                $save = $this->model->update($id, [
                    'value_setting' => $path . $fileName
                ]);

                //Simpan ke img_logo_resize
                $this->model->update(7, [
                    'value_setting' => $path . 'res_' . $fileName
                ]);

                if ($save) {
                    return $this->respond(["status" => true, "message" => lang('App.imgSuccess'), "data" => [$path . $fileName]], 200);
                } else {
                    return $this->respond(["status" => false, "message" => lang('App.imgFailed'), "data" => []], 200);
                }
            }
        }
    }
}
