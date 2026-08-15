<?php
/*
DEEPWATER SOLUTION
Website: https://www.deepwater.my.id
*/

use App\Modules\Auth\Models\LoginModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function publicKey()
{
    $publicKey = <<<EOD
		-----BEGIN PUBLIC KEY-----
		MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
		4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
		0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
		ehde/zUxo6UvS7UrBQIDAQAB
		-----END PUBLIC KEY-----
		EOD;
    return $publicKey;
}

function privateKey()
{
    $privateKey = <<<EOD
    -----BEGIN RSA PRIVATE KEY-----
    MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
    vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
    5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
    AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
    bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
    Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
    cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
    5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
    ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
    k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
    qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
    eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
    B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
    -----END RSA PRIVATE KEY-----
    EOD;
    return $privateKey;
}

/**
 * get access token from header
 * */
function getBearerToken($authenticationHeader): string
{
    // HEADER: Get the access token from the header
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }

    if (!empty($authenticationHeader)) {
        if (preg_match('/Bearer\s(\S+)/', $authenticationHeader, $matches)) {
            return $matches[1];
        } else {
            return $authenticationHeader;
        }
    }
    return null;
}

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = publicKey();
    $decodedToken = JWT::decode($encodedToken, new Key($key, 'RS256'));
    $userModel = new LoginModel();
    $userModel->findUserByEmailAddress($decodedToken->email);
}

function getSignedJWTForUser(string $email)
{
    $issuedAtTime = time();
    $tokenTimeToLive = env('JWT_TIME_TO_LIVE') ?? "28800000"; //Dalam milisecond = 480 menit atau 8 jam
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    $secretKey = privateKey();
    $jwt = JWT::encode($payload, $secretKey, 'RS256');
    return $jwt;
}

function createJWT(string $email, $expMinutes = 15)
{
    $issuedAtTime = time();
    $tokenExpiration = $issuedAtTime + ($expMinutes * 60);
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];
    $secretKey = privateKey();
    return JWT::encode($payload, $secretKey, 'HS256');
}

function decodeJWT($token)
{
    $secretKey = privateKey();
    return JWT::decode($token, new Key($secretKey, 'HS256'));
}