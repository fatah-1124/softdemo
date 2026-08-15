<?php

namespace App\Modules\Kontak\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Kontak\Models\KontakModel;
use App\Modules\Penjualan\Models\PenjualanModel;
use App\Modules\Log\Models\LogModel;

class Kontak extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = KontakModel::class;
    protected $penjualan;
    protected $log;

    public function __construct()
    {
        $this->penjualan = new PenjualanModel();
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
            'tipe' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'telepon' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'email' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $tipe = $json->tipe;
            $nama = $json->nama;
            $data = [
                'tipe' => $tipe,
                'grup' => $json->grup,
                'nama' => $nama,
                'perusahaan' => $json->perusahaan,
                'alamat' => $json->alamat,
                'telepon' => $json->telepon,
                'email' => $json->email,
                'nikktp' => $json->nikktp,
                'npwp' => $json->npwp,
            ];
        } else {
            $tipe = $this->request->getPost('tipe');
            $nama = $this->request->getPost('nama');
            $data = [
                'tipe' => $tipe,
                'grup' => $this->request->getPost('grup'),
                'nama' => $nama,
                'perusahaan' => $this->request->getPost('perusahaan'),
                'alamat' => $this->request->getPost('alamat'),
                'telepon' => $this->request->getPost('telepon'),
                'email' => $this->request->getPost('email'),
                'nikktp' => $this->request->getPost('nikktp'),
                'npwp' => $this->request->getPost('npwp'),
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
            $idKontak = $this->model->getInsertID();

            // Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Kontak ID: ' . $idKontak . '. Keterangan: ' . $nama . '(' . $tipe . ')' ]);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
                'lastID' => "$idKontak",
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'tipe' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'nama' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'telepon' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'tipe' => $json->tipe,
                'grup' => $json->grup,
                'nama' => $json->nama,
                'perusahaan' => $json->perusahaan,
                'alamat' => $json->alamat,
                'telepon' => $json->telepon,
                'email' => $json->email,
                'nikktp' => $json->nikktp,
                'npwp' => $json->npwp,
            ];
        } else {
            $data = $this->request->getRawInput();
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);

            // Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Update Kontak: ' . $id]);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);

        // Default ID 1 jangan dihapus, harus ada untuk menu kasir
        if ($id == 1) :
            $response = ['status' => false, 'message' => lang('App.delFailed'), 'data' => []];
            return $this->respond($response, 200);
        endif;
        //

        // Cek data Penjualan dulu karena akan terkena Foreign key cek 'restrict'
        $cekPenjualan = $this->penjualan->where(['id_kontak' => $id])->findAll();
        if ($cekPenjualan) :
            $response = [
                'status' => true,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        endif;

        if ($hapus) {
            $this->model->delete($id);

            // Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Kontak: ' . $id]);

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

    public function pelanggan()
    {
        $data = $this->model->where('tipe', 'Pelanggan')->findAll();
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

    public function vendor()
    {
        $data = $this->model->where('tipe', 'Vendor')->findAll();
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
}
