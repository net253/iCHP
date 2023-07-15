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
     public function createRequestForm()
     {
          try {
          
               $EmployeeNumber      = $this->request->getVar("EmployeeNumber");
               $PetitionerName      = $this->request->getVar("PetitionerName");
               $SNCCompany          = $this->request->getVar("SNCCompany");
               $Department          = $this->request->getVar("Department");
               $Phone               = $this->request->getVar("Phone");
               $WebsiteName         = $this->request->getVar("WebsiteName");
               $RequestType         = $this->request->getVar("RequestType");
               $RequirementDetails  = $this->request->getVar("RequirementDetails");
               $OperationDate       = $this->request->getVar("OperationDate");
               $ManagerFullName     = $this->request->getVar("ManagerFullName");
               $ManagerEmail        = $this->request->getVar("ManagerEmail");
               $InformationRequire  = $this->request->getVar("InformationRequire");

               $validate =  is_null($EmployeeNumber )    || is_null($PetitionerName)    || is_null($SNCCompany)    || is_null($Department)           ||         
                            is_null($Phone)              || is_null($WebsiteName)       || is_null($RequestType)   || is_null($RequirementDetails)   ||
                            is_null($OperationDate)      || is_null($ManagerFullName)   || is_null($ManagerEmail);
               
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

               $now = new \DateTime();

               $data = [
                    "EmployeeNumber"      =>  $EmployeeNumber,
                    "PetitionerName"      =>  $PetitionerName,
                    "SNCCompany"          =>  $SNCCompany,
                    "Department"          =>  $Department,
                    "Phone"               =>  $Phone,
                    "WebsiteName"         =>  $WebsiteName,
                    "RequestType"         =>  $RequestType,
                    "RequirementDetails"  =>  $RequirementDetails,
                    "OperationDate"       =>  $OperationDate,
                    "CreatedDT"           =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                    "ManagerFullName"     =>  $ManagerFullName,
                    "ManagerEmail"        =>  $ManagerEmail,
                    "ApprovalDT"          =>  null,
                    "ManagerRemarks"      =>  null,
                    "SatisfyScore"        =>  null,
                    "IsApprove"           =>  null,
                    "IsApproveOperator"   =>  null,
                    "OperatorApproveDT"   =>  null,
                    "OperatorRemarks"     =>  null,
                    "IsApproveSoftware"   =>  null,
                    "SoftwareApproveDT"   =>  null,
                    "SoftwareRemarks"     =>  null,
                    "InformationRequire"  =>  $InformationRequire,
                    "SendRequesttoApprovtorDT" =>  null,
               ]; 

               $this->dbiPMS->selectCollection("iCHP")->insertOne($data);

                // filter user from iCHP_Accounts collection for check users
                $pipeline = [
                    [
                    '$match' => [
                         'EmployeeNumber' => "$EmployeeNumber"
                         ]
                    ],
                    [
                    '$project' => [
                         '_id' => 0, 
                         'AccountID' => ['$toString' => '$_id'], 
                         'EmployeeNumber' => 1, 
                         'PetitionerName' => 1, 
                         ]
                    ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP_Accounts")->aggregate($pipeline);

               $users = array();
               foreach ($result as $doc) array_push($users, $doc);

               // Check users before insert or update data into collection
                if (empty($users)) {  // insert data
                    $data2 = [
                         "EmployeeNumber"      =>  $EmployeeNumber,
                         "PetitionerName"      =>  $PetitionerName,
                         "SNCCompany"          =>  $SNCCompany,
                         "Department"          =>  $Department,
                         "Phone"               =>  $Phone,
                    ];
                    $this->dbiPMS->selectCollection("iCHP_Accounts")->insertOne($data2);
                } else { // update data
                    $filter = ["EmployeeNumber" => "$EmployeeNumber"];
                    $dataforUpdate = [
                         "PetitionerName"      =>  $PetitionerName,
                         "SNCCompany"          =>  $SNCCompany,
                         "Department"          =>  $Department,
                         "Phone"               =>  $Phone,
                    ];
                    $this->dbiPMS->selectCollection("iCHP_Accounts")->updateOne($filter, ['$set' => $dataforUpdate]);
                }
              
               return $this->response->setJSON(["state" => true, "msg" => "สร้างคำร้องขอสำเร็จ"]);
          } catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }

     //TODO [POST] /Request/satisfy-score
     public function SatisfyScore()
     {
          try {
               $RequestID    = $this->request->getVar("RequestID");
               $SatisfyScore = $this->request->getVar("SatisfyScore");
               
               $validate =  is_null($RequestID) || is_null($SatisfyScore);
          
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

               $now = new \DateTime();

               $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
               $data = [
                    "SatisfyScore"  =>  $SatisfyScore,
               ];

               $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
          
               return $this->response->setJSON(["state" => true, "msg" => "บันทึกคะแนนแล้ว"]);
     } catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }

        //TODO [POST] /Request/approve-status
        public function approveStatus()
        {
            try {
                    $RequestID      = $this->request->getVar("RequestID");
                    $IsApprove      = $this->request->getVar("IsApprove");
                    $ManagerRemarks = $this->request->getVar("ManagerRemarks");
                    
                    $validate =  is_null($RequestID) || is_null($IsApprove);
                 
                   if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
    
                   $now = new \DateTime();
   
                   $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                   $data = [
                       "IsApprove"   =>  (boolean)$IsApprove,
                       "ManagerRemarks"     =>  $ManagerRemarks,
                       "ApprovalDT"   =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                   ];
   
                   $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
              
                    return $this->response->setJSON(["state" => true, "msg" => "อัพเดตสถานการดำเนินการแล้ว"]);
            } catch (\Exception $e) {
                    return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
            }
        }

     //TODO [POST] /Request/operator-status
     public function operatorStatus()
     {
         try {
                 $RequestID        = $this->request->getVar("RequestID");
                 $IsApproveOperator= $this->request->getVar("IsApproveOperator");
                 $OperatorRemarks  = $this->request->getVar("OperatorRemarks");
                 
                 $validate =  is_null($RequestID) || is_null($IsApproveOperator);
              
                if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
 
                $now = new \DateTime();

                $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                $data = [
                    "IsApproveOperator"   =>  (boolean)$IsApproveOperator,
                    "OperatorRemarks"     =>  $OperatorRemarks,
                    "OperatorApproveDT"   =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                ];

                $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
           
                 return $this->response->setJSON(["state" => true, "msg" => "อัพเดตสถานการดำเนินการแล้ว"]);
         } catch (\Exception $e) {
                 return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
         }
     }


      //TODO [POST] /Request/software-status
      public function softwareStatus()
      {
          try {
                  $RequestID        = $this->request->getVar("RequestID");
                  $IsApproveSoftware= $this->request->getVar("IsApproveSoftware");
                  $SoftwareRemarks  = $this->request->getVar("SoftwareRemarks");
                  
                  $validate =  is_null($RequestID) || is_null($IsApproveSoftware);
               
                 if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
  
                 $now = new \DateTime();
 
                 $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                 $data = [
                     "IsApproveSoftware"  =>  (boolean)$IsApproveSoftware,
                     "SoftwareRemarks"    =>  $SoftwareRemarks,
                     "SoftwareApproveDT"  =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                 ];
 
                 $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
            
                  return $this->response->setJSON(["state" => true, "msg" => "อัพเดตสถานการดำเนินการแล้ว"]);
          } catch (\Exception $e) {
                  return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
      }
     

     //TODO [GET] /Request/request-list
     public function requestList()
     {
          try {
               $pipeline = [
                    ['$project' => [
                         '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'EmployeeNumber' => 1, 'PetitionerName' => 1, 
                         'SNCCompany' => 1, 'Department' => 1, 'Phone' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'RequirementDetails' => 1, 
                         'OperationDate' => 1, 'CreatedDT' => ['$dateToString' => ['date' => '$CreatedDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']], 
                         'InformationRequire' => 1, 'ManagerFullName' => 1, 'ManagerEmail' => 1, 'IsApprove' => 1, 'ApprovalDT' => 1, 'ManagerRemarks' => 1, 
                         'IsApproveOperator' => 1, 'OperatorApproveDT' => 1, 'OperatorRemarks' => 1, 'IsApproveSoftware' => 1, 'SoftwareApproveDT' => 1, 'SoftwareRemarks' => 1,  'SatisfyScore' => 1,
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
      public function requestDetail()
      {

           try {
                $RequestID = $this->request->getVar("RequestID"); 

                $validate = is_null($RequestID); 

                if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
                
                $pipeline = [
                               [
                               '$match' => [
                                   '_id' => $this->MongoDBObjectId($RequestID)
                                   ]
                               ],
                               ['$project' => [
                                    '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'EmployeeNumber' => 1, 'PetitionerName' => 1, 
                                    'SNCCompany' => 1, 'Department' => 1, 'Phone' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'RequirementDetails' => 1, 
                                    'OperationDate' => 1, 'CreatedDT' => ['$dateToString' => ['date' => '$CreatedDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']], 
                                    'InformationRequire' => 1, 'ManagerFullName' => 1, 'ManagerEmail' => 1, 'IsApprove' => 1, 'ApprovalDT' => 1, 'ManagerRemarks' => 1, 
                                    'IsApproveOperator' => 1, 'OperatorApproveDT' => 1, 'OperatorRemarks' => 1, 'IsApproveSoftware' => 1, 'SoftwareApproveDT' => 1, 'SoftwareRemarks' => 1,  'SatisfyScore' => 1,
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


     //TODO [GET] /Request/project-name
     public function projectName()
     {
          try {
               $pipeline = [
                    ['$project' => [
                         '_id' => 0, 'NameID' => ['$toString' => '$_id'], 
                         'ProjectID' => 1, 
                         'ProjectName' => 1 
                         ]
                    ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP_ProjectName")->aggregate($pipeline);

               $data = array();
               foreach ($result as $doc) array_push($data, $doc);

               return $this->response->setJSON($data);   
     }  catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }


     //TODO [GET] /Request/snccompany-list
     public function sncCompanyList()
     {
          try {
               $pipeline = [
                    ['$project' => [
                         '_id' => 0, 'CompanyID' => ['$toString' => '$_id'], 
                         'ProjectID' => 1, 
                         'Company' => 1 ,
                         'CompanyFullName' => 1 ,
                         'CompanyFullNameEN' => 1 ,
                         'Department' => 1,
                         ]
                    ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP_SNCCompany")->aggregate($pipeline);

               $data = array();
               foreach ($result as $doc) array_push($data, $doc);

               return $this->response->setJSON($data);   
     }  catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }


}
?>