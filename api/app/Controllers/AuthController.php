<?php

namespace App\Controllers;

use App\Libraries\JWT\JWTUtils;
use App\Libraries\Bcrypt;

require __DIR__ . '/../Libraries/MongoDBLibs/vendor/autoload.php';

class AuthController extends BaseController
{
     private $bcrypt;
     // private $jwtUtils;
     private $mongo;
     private $dbiPMS;
     private $lineNotify;

     public function __construct()
     {
          $this->bcrypt = new Bcrypt(10);
          $this->jwtUtils = new JWTUtils();
          $this->mongo = new \MongoDB\Client("mongodb://iiot-center2:%24nc.ii0t%402o2E@10.0.0.8:27017/?authSource=admin");
          $this->dbiPMS = $this->mongo->selectDatabase("iPMS");
     }

     private function randomPassword(int $length = 8)
     {
          $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
          $pass = array();                        //remember to declare $pass as an array
          $alphaLength = strlen($alphabet) - 1;   //put the length -1 in cache
          for ($i = 0; $i < $length; $i++) {
               $n = rand(0, $alphaLength);
               $pass[] = $alphabet[$n];
          }
          return implode($pass); //turn the array into a string
     }

 
     //TODO [POST] /auth/login 
     public function login()
     {
          try {
               $Username = $this->request->getVar('Username');
               $Password = $this->request->getVar('Password');
               $validate = is_null($Username) || is_null($Password);
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

               //! Get users information
               //! check user
               $filter = ["Username" => $Username];
               $options = [
                    "limit" => 1,
                    "projection" => [
                         "_id"          => 0,
                         "AccountID"    => ['$toString' => '$_id'],
                         "FullNameTH"   => 1,
                         "FullNameEN"   => 1,
                         "Email"        => 1,
                         "Username"     => 1,
                         "Password"     => 1,
                         "Role"         => 1,
                         "Position"     => 1,
                         "LoggedInDT"   => 1,
                    ]
               ];
               $results = $this->dbiPMS->selectCollection("iCHP_Accounts")->find($filter, $options);
  
               $users = array();
               foreach ($results as $doc) array_push($users, $doc);

               if (count($users) === 0) return $this->response->setJSON(["state" => false, "msg" => "ไม่มีผู้ใช้งานนี้อยู่ในระบบ"]);

               \date_default_timezone_set('Asia/Bangkok');
               $now = new \DateTime();

               //! check password
               $user = (object)$users[0]; // first row
               $isPass = $this->bcrypt->verify($Password, $user->Password);
               if (!$isPass) return $this->response->setJSON(["state" => false, "msg" => "รหัสผ่านไม่ถูกต้อง"]);

               // Update LoggedInDT
               $filter = ["Username" => $Username];
               $update = ["LoggedInDT" => $this->MongoDBUTCDateTime($now->getTimestamp()*1000)];
               $this->dbiPMS->selectCollection("Accounts")->updateOne($filter, ['$set' => $update]);

               $payload = [
                    'AccountID'    => $user->AccountID,
                    'Username'     => $user->Username,
                    'Email'        => $user->Email,
                    'Role'         => $user->Role,
                    'Position'     => $user->Position,
                    'iat'          => $now->getTimestamp(), //! generate token time
                    'exp'          => $now->modify('+30000 hours')->getTimestamp() //! expire token time
                    // 'exp' => $now->modify('+40 seconds')->getTimestamp() //! expire token timeๅ
               ];
               $token = $this->jwtUtils->generateToken($payload);

               $timestamp = time() * 1000;

               return $this->response->setJSON([
                    "state"        => true, 
                    "msg"          => "เข้าสู่ระบบสำเร็จ", 
                    "FullNameEN"   => $user->FullNameEN,
                    "FullNameTH"   => $user->FullNameTH,
                    "Email"        => $user->Email,
                    "Username"     => $user->Username, 
                    "token"        => $token, 
                    "Role"         => $user->Role,
                    "Position"     => $user->Position
               ]);

          } catch (\Exception $e) {
               // return $this->response->setStatusCode(500)->setJSON(["state" => false, "msg" => $e->getMessage()]);
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }

     //TODO [POST] /auth/change-password          -- ใช้งานได้ แต่ไม่ได้ใช้ในระบบ --
     public function changePassword()
     {
          try {
               //! Request validation
               //! JWT Check Token
               $header = $this->request->getServer('HTTP_AUTHORIZATION');
               $jwt = $this->jwtUtils->verifyToken($header);
               if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
               $decoded = $jwt->decoded;

               //! Body
               $oldPassword = $this->request->getVar('oldPassword');
               $newPassword = $this->request->getVar('newPassword');
               $validate = is_null($oldPassword) || is_null($newPassword);
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
               //! ./Request validation

               //! Get user information & check old pasword
               $filter = ["_id" => $this->MongoDBObjectId($decoded->AccountID)];
               $options = [
                    "limit" => 1,
                    "projection" => [
                         "Password" => 1
                    ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP_Accounts")->find($filter, $options);

               $users = array();
               foreach ($result as $doc) array_push($users, $doc);

               if (count($users) === 0) return $this->response->setJSON(["state" => false, "msg" => "ไม่มีผู้ใช้งานนี้อยู่ในระบบ"]);
               $user = (object)$users[0]; // first row
               $isPass = $this->bcrypt->verify($oldPassword, $user->Password); //! Compare password
               if (!$isPass) return $this->response->setJSON(["state" => false, "msg" => "รหัสผ่านไม่ถูกต้อง"]);

               // //! Hash new password & Update to DB
               $hash = $this->bcrypt->hash($newPassword);
               $filter = ["_id" => $this->MongoDBObjectId($decoded->AccountID)];
               $update = ["Password" => $hash];
               $this->dbiPMS->selectCollection("iCHP_Accounts")->updateOne($filter, ['$set' => $update]);

               return $this->response->setJSON(["state" => true, "msg" => "เปลี่ยนรหัสผ่านสำเร็จ"]);
          } catch (\Exception $e) {
               // return $this->response->setStatusCode(500)->setJSON(["state" => false, "msg" => $e->getMessage()]);
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }

     //TODO [POST] /auth/forgot-password          -- ใช้งานได้ แต่ไม่ได้ใช้ในระบบ --
     public function forgotPassword()
     {
          try {

               $Email = $this->request->getVar('Email');
               $validate = is_null($Email);
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
               //! ./Request validation

               $pipeline = [
                         ['$match' => ['Email' => $Email]], 
                         ['$project' => [
                              '_id'       => 0, 
                              'AccountID' => ['$toString' => '$_id'], 
                              'Username'  => 1]
                         ]
                    ];
               $result = $this->dbiPMS->selectCollection("iCHP_Accounts")->aggregate($pipeline);
     
               $users = array();
               foreach ($result as $doc) array_push($users, $doc);
               if (count($users) === 0) return $this->response->setJSON(["state" => false, "msg" => "ไม่มี Email นี้ในระบบ"]);

               $user = (object)$users[0];
               $newPassword = $this->randomPassword(6); //! Random new password length 6 character
               // return $this->response->setJSON($user->Username);

               date_default_timezone_set('Asia/Bangkok');
               $now = new \DateTime();

               $data = [
                    'Subject'      => "iSAS (reset password)",
                    'Name'         => $user->Username,
                    'Email'        => $Email,
                    'NewPassword'  => $newPassword,
                    'ReqDT'        => $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                    'SentMailPasswordDT' => null,
               ];

               $this->dbiPMS->selectCollection("MailNotifyForgotPassword")->insertOne($data);

                // //! Hash new password & Update 
                $hash = $this->bcrypt->hash($newPassword);
                $filter = ["_id" => $this->MongoDBObjectId($user->AccountID)];
                $update = ["Password" => $hash];
                $this->dbiPMS->selectCollection("iCHP_Accounts")->updateOne($filter, ['$set' => $update]);
                
                return $this->response->setJSON(["state" => true, "msg" => "รีเซ็ตรหัสผ่านสำเร็จ ส่งรหัสผ่านใหม่ไปยัง Email แล้ว"]);

          } catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }

     //TODO [POST] /auth/checkAuth-password
     public function checkAuth()
     {
          try {
               //! JWT
               $header = $this->request->getServer('HTTP_AUTHORIZATION');
               $jwt = $this->jwtUtils->verifyToken($header);
               if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, "msg" => "ปฏิเสธการเข้าถึง API"]);
               // $decoded = $jwt->decoded;
               return $this->response->setJSON(["state" => true, "msg" => "TOKEN ยังไม่หมดอายุ"]);
          } catch (\Exception $e) {
               // return $this->response->setStatusCode(500)->setJSON(["state" => false, "msg" => $e->getMessage()]);
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }
}

