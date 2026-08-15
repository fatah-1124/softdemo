<?php

namespace App\Modules\Auth\Controllers;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Auth\Models\LoginModel;
use App\Modules\Log\Models\LoginLogModel;
use App\Modules\Log\Models\LogModel;

class Auth extends BaseController
{
	protected $setting;
	protected $log;

	public function __construct()
	{
		$this->setting = new Settings();
		$this->log = new LogModel();
	}
	public function login()
	{
		if ($this->session->logged_in == true) {
			$this->session->setFlashdata('success', 'You have successfully logged in');
			return redirect()->to('/dashboard');
		}

		return view('App\Modules\Auth\Views/login', [
			'img_background' => $this->setting->info['img_background'],
		]);
	}

	public function register()
	{
		if ($this->session->logged_in == true) {
			$this->session->setFlashdata('success', 'You have successfully logged in');
			return redirect()->to('/dashboard');
		}

		return view('App\Modules\Auth\Views/register', [
			'img_background' => $this->setting->info['img_background'],
		]);
	}

	public function verifyEmail()
	{
		$input = $this->request->getVar();

		$rules = [
			'email' => [
				'rules'  => 'required',
				'errors' => []
			],
			'token' => [
				'rules'  => 'required',
				'errors' => []
			],
		];

		if (!$this->validate($rules)) {
			return redirect()->to(base_url());
		}

		$user_model = new LoginModel();
		$user = $user_model->where(['email' => $input['email'], 'token' => $input['token']])->first();
		$user_data = [
			'active' => 1,
		];
		$user_model->update($user['user_id'], $user_data);
		return redirect()->to(base_url());
	}

	public function passwordReset()
	{
		if (isset($this->session->username)) return redirect()->to(base_url('dashboard'));
		
		return view('App\Modules\Auth\Views\password/reset', [
			'img_background' => $this->setting->info['img_background'],
		]);
	}

	public function passwordChange()
	{
		if (isset($this->session->username)) return redirect()->to(base_url('dashboard'));
		$rules = [
			'email' => [
				'rules'  => 'required',
				'errors' => []
			],
			'token' => [
				'rules'  => 'required',
				'errors' => []
			],
		];
		if (!$this->validate($rules)) {
			return redirect()->to(base_url());
		}
		$data = $this->request->getVar();
		return view('App\Modules\Auth\Views\password/change', $data);
	}

	public function logout()
	{
		//$this->session->destroy();
		// Update Login Log
		$loginLog = new LoginLogModel();
		$query = $loginLog->where('loggedin_at', $this->session->logged_in_at)->first();
		if ($query) :
			$id = $query['id_log_login'];
			$loginLog->update($id, ['loggedout_at' => date('Y-m-d H:i:s')]);
		endif;
		$data = ['id', 'email', 'nama', 'username', 'role', 'logged_in', 'logged_in_at'];
		//Save Log
		$this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Logout at: ' . date('Y-m-d H:i:s')]);
		// Hapus session data
		$this->session->remove($data);
		$this->session->setFlashdata('success', 'You have successfully logged out');
		// Hapus Cookie access_token
		if (isset($_COOKIE['access_token'])) {
			unset($_COOKIE['access_token']);
			setcookie('access_token', '', time() - 3600, '/'); // empty value and old timestamp
		}
		return redirect()->to('/login');
	}
}
