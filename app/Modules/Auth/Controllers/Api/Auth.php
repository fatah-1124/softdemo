<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Auth\Models\LoginModel;
use App\Modules\Group\Models\LoginGroupModel;
use App\Modules\Log\Models\LoginLogModel;
use App\Modules\Log\Models\LogModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Auth extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = LoginModel::class;
    protected $log;
    protected $group;

    public function __construct()
    {
        $this->log = new LogModel();
        $this->group = new LoginGroupModel();
    }

    /**
     * Register a new user
     * @return Response
     * @throws ReflectionException
     */
    public function register()
    {
        $rules = [
            'username' => 'required',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[user.email]',
            'password' => 'required|min_length[8]|max_length[255]'
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules)) {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => $this->validator->getErrors()
                ],
                ResponseInterface::HTTP_OK
            );
        }

        $token = base64_encode(mt_rand(100000, 999999));
        $data = [
            'email' => $input['email'],
            'username' => $input['username'],
            'password' => $input['password'],
            'token' => $token
        ];

        if ($this->model->save($data)) {
            helper('email');
            sendEmail("Verifikasi Akun", $input['email'], view('email/verify', $data));
            return $this->getResponse(
                [
                    'status' => true,
                    'message' => lang('App.regSuccess'),
                    'data' => ['url' => base_url("")]
                ],
                ResponseInterface::HTTP_OK
            );
        } else {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => lang('App.regFailed'),
                    'data' => []
                ],
                ResponseInterface::HTTP_OK
            );
        }
    }

    /**
     * Authenticate Existing User
     * @return Response
     */
    public function login()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email|validateUser[email,password]',
            'password' => 'required|min_length[8]|max_length[255]|validateUser[email, password]'
        ];

        $errors = [
            'email' => ['validateUser' => lang('App.errorLogin')],
            'password' => ['validateUser' => lang('App.errorPassword')]
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules, $errors)) {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => lang('App.invalid'),
                    'data' => $this->validator->getErrors()
                ],
                ResponseInterface::HTTP_OK
            );
        }

        return $this->getJWTForUser($input['email']);
    }

    /**
     * Request Reset Password for user
     * @return Response
     * @throws ReflectionException
     */
    public function resetPassword()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_not_unique[user.email]',
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules)) {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => $this->validator->getErrors()
                ],
                ResponseInterface::HTTP_OK
            );
        }

        $token = base64_encode(mt_rand(100000, 999999));
        $data = [
            'email' => $input['email'],
            'token' => $token,
        ];

        $user = $this->model->where(['email' => $input['email']])->first();
        $user_id = $user['id_login'];
        $user_data = [
            'token' => $token,
        ];

        if ($this->model->update($user_id, $user_data)) {
            helper('email');
            sendEmail("Permintaan Reset Password", $input['email'], view('email/reset', $data));
            return $this->getResponse(
                [
                    'status' => true,
                    'message' => lang('App.checkEmail'),
                    'data' => ['url' => base_url("")]
                ],
                ResponseInterface::HTTP_OK
            );
        } else {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => lang('App.reqFailed'),
                    'data' => []
                ],
                ResponseInterface::HTTP_OK
            );
        }
    }

    /**
     * Request Change password for user
     * @return Response
     * @throws ReflectionException
     */
    public function changePassword()
    {
        $rules = [
            'email' => 'required',
            'token' => 'required',
            'password' => 'required|min_length[8]|max_length[255]',
            'verify' => 'required|matches[password]'
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules)) {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => $this->validator->getErrors()
                ],
                ResponseInterface::HTTP_OK
            );
        }

        $forgot_pass = $this->model->where(['email' => $input['email'], 'token' => $input['token']])->first();
        if (!$forgot_pass) {
            return $this->getResponse(["status" => false, "message" => lang('App.tokenInvalid'), "data" => []], ResponseInterface::HTTP_OK);
        }

        $user = $this->model->where(['email' => $input['email']])->first();
        $user_id = $user['id_login'];
        $user_data = [
            'password' => $input['password'],
        ];
        if ($this->model->update($user_id, $user_data)) {
            return $this->getResponse(
                [
                    'status' => true,
                    'message' => lang('App.passChanged'),
                    'data' => ['url' => base_url("/login")]
                ],
                ResponseInterface::HTTP_OK
            );
        } else {
            return $this->getResponse(
                [
                    'status' => false,
                    'message' => lang('App.regFailed'),
                    'data' => []
                ],
                ResponseInterface::HTTP_OK
            );
        }
    }

    private function getJWTForUser(
        string $emailAddress,
        int $responseCode = ResponseInterface::HTTP_OK
    ) {
        try {
            $user = $this->model->findUserByEmailAddress($emailAddress);
            unset($user['password']);

            helper('jwt');

            $now = date('Y-m-d H:i:s');

            $group = $this->group->getGroupById($user['id_login']);

            $setSession = [
                'id' => $user['id_login'],
                'email' => $user['email'],
                'nama' => $user['nama'],
                'username' => $user['username'],
                'role' => $group['id_group'],
                'group' => $group['nama_group'],
                'logged_in' => true,
                'logged_in_at' => $now
            ];
            $this->session->set($setSession);

            //Login Log
            $loginLog = new LoginLogModel();
            //Cek apakah ada riwayat Login
            $cekLogin = $loginLog->where(['email' => $user['email'], 'loggedout_at' => null])->findAll();
            foreach ($cekLogin as $cek) :
                $idLoginLog = $cek['id_log_login'];
                $loginLog->update($idLoginLog, ['loggedout_at' => date('Y-m-d H:i:s')]);
            endforeach;
            //Simpan Login Log
            $loginLog->save(
                [
                    'email' => $user['email'],
                    'username' => $user['username'],
                    'loggedin_at' => $now
                ]
            );

            setcookie("access_token", getSignedJWTForUser($emailAddress), time() + 7200, "/", "", false, true);

            //Save Log
            // User Agent Class
            $agent = $this->request->getUserAgent();
            if ($agent->isBrowser()) {
                $currentAgent = $agent->getPlatform() . '/' . $agent->getBrowser() . ' ' . $agent->getVersion();
            } elseif ($agent->isRobot()) {
                $currentAgent = $agent->getRobot();
            } elseif ($agent->isMobile()) {
                $currentAgent = $agent->getMobile();
            } else {
                $currentAgent = 'Unidentified User Agent';
            }
            $this->log->save(['keterangan' => session('nama') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Login at: ' . $now . ' on device/s: ' . $currentAgent]);

            return $this->getResponse(
                [
                    'status' => true,
                    'message' => lang('App.authSuccess'),
                    'data' => $user,
                    'access_token' => getSignedJWTForUser($emailAddress),
                    'appdata' => ['username' => $user, 'token' => getSignedJWTForUser($emailAddress)]
                ]
            );
        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'status' => false,
                    'error' => $exception->getMessage()
                ],
                $responseCode
            );
        }
    }
}
