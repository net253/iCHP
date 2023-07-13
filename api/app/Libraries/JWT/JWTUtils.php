<?php

namespace App\Libraries\JWT;

use App\Libraries\JWT\JWT;
use App\Libraries\JWT\Key;

define("PRIVATE_KEY", "-----BEGIN RSA PRIVATE KEY-----
MIIBOAIBAAJAZuJ6EtUJaeH7ZEq4Sxi+3ZK/+iR1vsZxy/6RjXKxtnDwbi3jKRQh
SXk3V3AKc/3hYtx44J731kODW4/hsu35aQIDAQABAkAJg9aAWV12gmTKgLKMl2xH
d6PzkV2mWBn8IL37U+klkyvmaYxEKNxTgB9m6T1v8No27xrE0cBFhqZi0SWVg21F
AiEAwC24AGL1aFdn17mRLtqVE443F1xS19cFdlTQs56AHZcCIQCJDVWhUYoyG+fx
YELyBQSGtk5ifdAnoVovQtPxnEOA/wIgEkKF0CuW68IaUMoF/HCyZ3hEzchs6qs4
jqTCa76sp6MCIHlmw3SLqzPp/lKVZ5fFFBZUhSi/s9R3HFEDDIVYW393AiACeVBv
8tMNPDwZ7+sLiA8C5lqT5ZXbc1VCU3o8jbkxgg==
-----END RSA PRIVATE KEY-----");

class JWTUtils
{
     public function generateToken($payload)
     {
          $token = JWT::encode($payload, PRIVATE_KEY, 'HS256');
          return $token;
     }

     public function verifyToken($header)
     {
          $token = null;
          // extract the token from the header
          if (!empty($header)) {
               if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                    $token = $matches[1];
               }
          }

          // check if token is null or empty
          if (is_null($token) || empty($token)) {
               return (object)['state' => false, 'msg' => 'Access denied', 'decoded' => []];
          }

          try {
               $decoded = JWT::decode($token, new Key(PRIVATE_KEY, 'HS256'));
               return (object)['state' => true, 'msg' => 'OK', 'decoded' => $decoded];
          } catch (\Exception $e) {
               return (object)['state' => false, 'msg' => $e->getMessage(), 'decoded' => []];
          }
     }
}
