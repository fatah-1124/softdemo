<?php

namespace App\Modules\Backup\Controllers\Api;
/*
PT ITSHOP BISNIS DIGITAL
Website: https://itshop.biz.id
Toko Online: ITSHOP Purwokerto (https://Tokopedia.com/itshoppwt, https://Shopee.co.id/itshoppwt, https://Toco.id/store/itshop-purwokerto)
Dibuat oleh: Hari Wicaksono, S.Kom
06-2022
*/

use App\Controllers\BaseControllerApi;
use App\Modules\Backup\Models\BackupModel;
use App\Modules\Log\Models\LogModel;
use Y0lk\SQLDumper\SQLDumper;

class Backup extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = BackupModel::class;
    protected $log;

    public function __construct()
    {
        $this->log = new LogModel();
        helper('filesystem');
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

    public function create()
    {
        try {
            $db = \Config\Database::connect();
	        $dbname = $db->database;
            $path = WRITEPATH . 'uploads/backups/';

            //Y0lk\SQLDumper\SQLDumper 
            $tanggal = date('dmy-His');
            $filename = $dbname.'-' . $tanggal . '.sql';
            $hostName = env('database.default.hostname');
            $databaseName = env('database.default.database');
            $userName = env('database.default.username');
            $password = env('database.default.password');
            //Init the dumper with your DB info
            $dumper = new SQLDumper($hostName, $databaseName, $userName, $password);
            //Set all tables to dump without data and without DROP statement
            $dumper->allTables()
                ->withData(true)
                ->withDrop(true);
            //This will group DROP statements and put them at the beginning of the dump
            //$dumper->groupDrops(true);
            //This will group INSERT statements and put them at the end of the dump
            //$dumper->groupInserts(true);
            $dumper->save($path . $filename);

            $data = array(
                'file_name' => $filename,
                'file_path' => $path . $filename,
                'created_at' => date('Y-m-d H:i:s')
            );
            //save ke tabel
            $this->model->save($data);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Save Backup: ' . $filename]);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } catch (\Exception $e) {
            $response = [
                'status' => true,
                'message' => $e->getMessage(),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        $filepath = $hapus['file_path'];
        if ($hapus) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            $this->model->delete($id);

            //Save Log
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Delete Backup: ' . $id]);

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

    public function download()
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $id = $json->id;
        } else {
            $id = $this->request->getPost('id');
        }

        $backup = $this->model->find($id);
        $name = $backup['file_name'];
        $path = $backup['file_path'];
        $fileName = $name;
        $filePath = file_get_contents($path);

        //Save Log
        $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Download Backup: ' . $id]);

        $response = [
            'status' => true,
            'message' => lang('App.getSuccess'),
            'data' => ['filename' => $fileName, 'url' => $filePath],
        ];
        return $this->respond($response, 200);
    }
}
