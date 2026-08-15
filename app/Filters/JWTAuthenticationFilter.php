<?php

namespace App\Filters;
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class JWTAuthenticationFilter implements FilterInterface
{
    use ResponseTrait;

    /** 
     * Get hearder Authorization
     * */
    function getAuthorizationHeader()
    {
        $headers = null;
        if (!empty($_COOKIE['access_token'])) {
            $headers = $_COOKIE['access_token'];
        } else if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        //$authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');
        $authenticationHeader = $this->getAuthorizationHeader();

        try {
            helper('jwt');
            //$encodedToken = getJWTFromRequest($authenticationHeader);
            $encodedToken = getBearerToken($authenticationHeader);
            validateJWTFromRequest($encodedToken);
            return $request;
        } catch (Exception $e) {
            if ($e->getMessage() == "Expired token") {
                return Services::response()->setJSON(['expired' => true, 'message' => $e->getMessage(), 'data' => ['url' => base_url("/logout")]])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            } else if ($e->getMessage() == "Missing or invalid JWT in request") {
                return Services::response()->setJSON(['expired' => true, 'message' => $e->getMessage(), 'data' => ['url' => base_url("/logout")]])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            } else {
                return Services::response()->setJSON(['message' => $e->getMessage()])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
