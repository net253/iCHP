<?php

namespace App\Controllers;
use App\Libraries\JWT\JWTUtils;
use CodeIgniter\HTTP\Response;
use App\Models\FlexModel;

require __DIR__ . '/../Libraries/MongoDBLibs/vendor/autoload.php';

class RequestController extends BaseController
{
       // private $jwtUtils;
       private $flexModel;
       private $mongo;
       private $dbiPMS;
       private $lineNotify;
     public function __construct()
     {
          $this->jwtUtils  = new JWTUtils();
          $this->flexModel = new FlexModel();
          $this->mongo = new \MongoDB\Client("mongodb://iiot-center2:%24nc.ii0t%402o2E@10.0.0.8:27017/?authSource=admin");
          $this->dbiPMS = $this->mongo->selectDatabase("iPMS");
     }

     //TODO [POST] /Request/create-requestform
//     public function createRequestForm()
//     {
//         try {
//                 //! Request validation
//                 //! JWT
//                 $header = $this->request->getServer('HTTP_AUTHORIZATION');
//                 $jwt = $this->jwtUtils->verifyToken($header);
//                 if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
//                 $decoded = $jwt->decoded;
            
//                 $EmployeeNumber      = $this->request->getVar("EmployeeNumber");
//                 $PetitionerName      = $this->request->getVar("PetitionerName");
//                 $SNCCompany          = $this->request->getVar("SNCCompany");
//                 $AgencyName          = $this->request->getVar("AgencyName");
//                 $Phone               = $this->request->getVar("Phone");
//                 $WebsiteName         = $this->request->getVar("WebsiteName");
//                 $RequestType         = $this->request->getVar("RequestType");
//                 $RequirementDetails  = $this->request->getVar("RequirementDetails");
//                 $OperationDate       = $this->request->getVar("OperationDate");
//                 $InformationRequire  = $this->request->getVar("InformationRequire");
               
  
//                 $validate =  is_null($EmployeeNumber )    || is_null($PetitionerName)    || is_null($SNCCompany)    || is_null($AgencyName)           ||         
//                              is_null($Phone)              || is_null($WebsiteName)       || is_null($RequestType)   || is_null($RequirementDetails)   ||
//                              is_null($OperationDate);
             
//                if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

//                $now = new \DateTime();

//                $data = [
//                     "EmployeeNumber"      =>  $EmployeeNumber,
//                     "PetitionerName"      =>  $PetitionerName,
//                     "SNCCompany"          =>  $SNCCompany,
//                     "AgencyName"          =>  $AgencyName,
//                     "Phone"               =>  $Phone,
//                     "WebsiteName"         =>  $WebsiteName,
//                     "RequestType"         =>  $RequestType,
//                     "RequirementDetails"  =>  $RequirementDetails,
//                     "OperationDate"       =>  $OperationDate,
//                     "CreatedDT"           =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
//                     "InformationRequire"  =>  $InformationRequire,
//                     "ManagerFullName"     =>  null,
//                     "ManagerEmail"        =>  null,
//                     "IsApprove"           =>  null,
//                     "ApprovalDate"        =>  null,
//                     "ManagerRemarks"      =>  null,
//                     "OperatorFullName"    =>  null,
//                     "OperatorDate"        =>  null,
//                     "OperatorRemarks"     =>  null,
//                     "SendRequesttoApprovtorDT"     =>  null,
//                ];

//                // "InformationRequire"  =>  !in_array(!$RequestType, ['ขอเพิ่มบัญชีผู้ใช้งาน', 'ขอปิดบัญชีผู้ใช้งาน']) ? $data["InformationRequire"] = $InformationRequire : $data["InformationRequire"] = null,


//                 $this->dbiPMS->selectCollection("iCHP")->insertOne($data);
          
//                 return $this->response->setJSON(["state" => true, "msg" => "สร้างคำร้องขอสำเร็จ"]);
//         } catch (\Exception $e) {
//                 return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
//         }
//     }

 //TODO [POST] /Request/create-requestform
 public function createRequestForm()
 {
     try {
             //! Request validation
             //! JWT
             $header = $this->request->getServer('HTTP_AUTHORIZATION');
             $jwt = $this->jwtUtils->verifyToken($header);
             if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
             $decoded = $jwt->decoded;
         
             $EmployeeNumber      = $this->request->getVar("EmployeeNumber");
             $PetitionerName      = $this->request->getVar("PetitionerName");
             $SNCCompany          = $this->request->getVar("SNCCompany");
             $AgencyName          = $this->request->getVar("AgencyName");
             $Phone               = $this->request->getVar("Phone");
             $WebsiteName         = $this->request->getVar("WebsiteName");
             $RequestType         = $this->request->getVar("RequestType");
             $RequirementDetails  = $this->request->getVar("RequirementDetails");
             $OperationDate       = $this->request->getVar("OperationDate");
             $InformationRequire  = $this->request->getVar("InformationRequire");
            

             $validate =  is_null($EmployeeNumber )    || is_null($PetitionerName)    || is_null($SNCCompany)    || is_null($AgencyName)           ||         
                          is_null($Phone)              || is_null($WebsiteName)       || is_null($RequestType)   || is_null($RequirementDetails)   ||
                          is_null($OperationDate);
          
            if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

            $now = new \DateTime();

            $data = [
                 "EmployeeNumber"      =>  $EmployeeNumber,
                 "PetitionerName"      =>  $PetitionerName,
                 "SNCCompany"          =>  $SNCCompany,
                 "AgencyName"          =>  $AgencyName,
                 "Phone"               =>  $Phone,
                 "WebsiteName"         =>  $WebsiteName,
                 "RequestType"         =>  $RequestType,
                 "RequirementDetails"  =>  $RequirementDetails,
                 "OperationDate"       =>  $OperationDate,
                 "CreatedDT"           =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                 "InformationRequire"  =>  $InformationRequire,
                 "ManagerFullName"     =>  null,
                 "ManagerEmail"        =>  null,
                 "IsApprove"           =>  null,
                 "ApprovalDate"        =>  null,
                 "ManagerRemarks"      =>  null,
                 "OperatorFullName"    =>  null,
                 "OperatorDate"        =>  null,
                 "OperatorRemarks"     =>  null,
                 "SendRequesttoApprovtorDT"     =>  null,
            ];

             $this->dbiPMS->selectCollection("iCHP")->insertOne($data);
       
             return $this->response->setJSON(["state" => true, "msg" => "สร้างคำร้องขอสำเร็จ"]);
     } catch (\Exception $e) {
             return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
     }
 }



     //TODO [POST] /Request/add-useraccount
     public function addUserAccount()
     {
         try {
                 //! Request validation
                 //! JWT
                 $header = $this->request->getServer('HTTP_AUTHORIZATION');
                 $jwt = $this->jwtUtils->verifyToken($header);
                 if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
                 $decoded = $jwt->decoded;
             
                 $RequestID          = $this->request->getVar("RequestID");
                 $ManagerFullName    = $this->request->getVar("ManagerFullName");
                 $ManagerEmail       = $this->request->getVar("ManagerEmail");
                 $ApprovalDate       = $this->request->getVar("ApprovalDate");
                 $ManagerRemarks     = $this->request->getVar("ManagerRemarks");
                 $OperatorFullName   = $this->request->getVar("OperatorFullName");
                 $OperatorDate       = $this->request->getVar("OperatorDate");
                 $OperatorRemarks    = $this->request->getVar("OperatorRemarks");
                
   
                 $validate =  is_null($RequestID )          || is_null($ManagerFullName)    || is_null($ManagerEmail)   || is_null($ApprovalDate)    ||         
                              is_null($ManagerRemarks)      || is_null($OperatorFullName)   || is_null($OperatorDate)   || is_null($OperatorRemarks);
              
                if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
 
                $now = new \DateTime();

                $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                $data = [
                    "ManagerFullName"     =>  $ManagerFullName,
                    "ManagerEmail"        =>  $ManagerEmail,
                    "ApprovalDate"        =>  $ApprovalDate,
                    "ManagerRemarks"      =>  $ManagerRemarks,
                    "OperatorFullName"    =>  $OperatorFullName,
                    "OperatorDate"        =>  $OperatorDate,
                    "OperatorRemarks"     =>  $OperatorRemarks,
                    
                ];

               $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
           
                 return $this->response->setJSON(["state" => true, "msg" => "สร้างการอนุมัติแล้ว"]);
         } catch (\Exception $e) {
                 return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
         }
     }
     //TODO [POST] /Request/approve-status
     public function approveStatus()
     {
         try {
                 //! Request validation
                 //! JWT
                 $header = $this->request->getServer('HTTP_AUTHORIZATION');
                 $jwt = $this->jwtUtils->verifyToken($header);
                 if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
                 $decoded = $jwt->decoded;
             
                 $RequestID          = $this->request->getVar("RequestID");
                 $IsApprove          = $this->request->getVar("IsApprove");
                 $ManagerRemarks     = $this->request->getVar("ManagerRemarks");
                 
                 $validate =  is_null($RequestID) || is_null($IsApprove);
              
                if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
 
                $now = new \DateTime();

                $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                $data = [
                    "IsApprove"       =>  (boolean)$IsApprove,
                    "ManagerRemarks"  =>  $ManagerRemarks,
                ];

               $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
           
                 return $this->response->setJSON(["state" => true, "msg" => "สร้างการอนุมัติแล้ว"]);
         } catch (\Exception $e) {
                 return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
         }
     }


     //TODO [GET] /Request/request-list
     public function RequestList()
     {
          try {
               //! Request validation
               //! JWT
               $header = $this->request->getServer('HTTP_AUTHORIZATION');
               $jwt = $this->jwtUtils->verifyToken($header);
               if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
               $decoded = $jwt->decoded;
               
               $pipeline = [
                              ['$project' => [
                                   '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'EmployeeNumber' => 1, 'PetitionerName' => 1, 
                                   'SNCCompany' => 1, 'AgencyName' => 1, 'Phone' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 
                                   'RequirementDetails' => 1, 'OperationDate' => 1, 'CreatedDT' => 1, 'InformationRequire' => 1, 
                                   'ManagerFullName' => 1, 'ManagerEmail' => 1, 'IsApprove' => 1, 'ApprovalDate' => 1, 'ManagerRemarks' => 1, 
                                   'OperatorFullName' => 1, 'OperatorDate' => 1, 'OperatorRemarks' => 1, 'SendRequesttoApprovtorDT' => 1
                                   ]
                              ]
                         ];
               $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);

               $data = array();
               foreach ($result as $doc) array_push($data, $doc);

               return $this->response->setJSON($data);   
      }  catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }


      //TODO [GET] /Request/request-detail
      public function RequestDetail()
      {
           try {
                //! Request validation
                //! JWT
                $header = $this->request->getServer('HTTP_AUTHORIZATION');
                $jwt = $this->jwtUtils->verifyToken($header);
                if (!$jwt->state) return $this->response->setStatusCode(401)->setJSON(['state' => false, 'msg' => 'ปฏิเสธการเข้าถึง API']);
                $decoded = $jwt->decoded;

                $RequestID = $this->request->getVar("RequestID"); 
                
                $pipeline = [
                               ['$project' => [
                                    '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'EmployeeNumber' => 1, 'PetitionerName' => 1, 
                                    'SNCCompany' => 1, 'AgencyName' => 1, 'Phone' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 
                                    'RequirementDetails' => 1, 'OperationDate' => 1, 'CreatedDT' => 1, 'InformationRequire' => 1, 
                                    'ManagerFullName' => 1, 'ManagerEmail' => 1, 'IsApprove' => 1, 'ApprovalDate' => 1, 'ManagerRemarks' => 1, 
                                    'OperatorFullName' => 1, 'OperatorDate' => 1, 'OperatorRemarks' => 1, 'SendRequesttoApprovtorDT' => 1
                                    ]
                               ]
                          ];
                $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);
 
                $data = array();
                foreach ($result as $doc) array_push($data, $doc);
 
                return $this->response->setJSON($data[0]);   
       }  catch (\Exception $e) {
                return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
           }
      }

}
?>