<?php

namespace App\Controllers;
use App\Libraries\JWT\JWTUtils;
use App\Libraries\LineNotify;

require __DIR__ . '/../Libraries/MongoDBLibs/vendor/autoload.php';

class RequestController extends BaseController
{
       // private $jwtUtils;
       private $mongo;
       private $dbiPMS;
       private $lineNotify;
     public function __construct()
     {
          $this->lineNotify = new LineNotify("lKDI8kD68mkTpOC70aoEWwiOfCgrsezJi2aYTr7KqXN");   // set Token ใช้งานจริง
          // $this->lineNotify = new LineNotify("aFLUoCrwhZYZBZv6H4MPwMtUBNNv7qCrIDhwywtKBJ8");   // Token ทดสอบของตัวเอง
          $this->jwtUtils  = new JWTUtils();
          $this->mongo = new \MongoDB\Client("mongodb://iiot-center2:%24nc.ii0t%402o2E@10.0.0.8:27017/?authSource=admin");
          $this->dbiPMS = $this->mongo->selectDatabase("iPMS");
     }

     //TODO [POST] /Request/create-requestform
     public function createRequestForm()
     {
          try {          
               $EmployeeNumber      = $this->request->getVar("EmployeeNumber");
               $PetitionerName      = $this->request->getVar("PetitionerName");
               $PetitionerEmail     = $this->request->getVar("PetitionerEmail");
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
               $SatisfyScore        = $this->request->getVar("SatisfyScore");

               $validate =  is_null($EmployeeNumber )  || is_null($PetitionerName)    || is_null($SNCCompany)    || is_null($Department)           ||         
                            is_null($Phone)            || is_null($WebsiteName)       || is_null($RequestType)   || is_null($RequirementDetails)   ||
                            is_null($OperationDate)    || is_null($ManagerFullName)   || is_null($ManagerEmail)  || is_null($PetitionerEmail);
               
               if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);

               // Gen RunNo
               $now = new \DateTime();
               $Date = $now->format('dmy');
     
               $filter = [];
               $options = [
                    "sort" => ["RunNo" => -1],
                    "limit" => 1,
                    "projection" => [
                         "_id" => 0,
                         "RunNo" => 1,
                    ],
               ];

               $result = $this->dbiPMS->selectCollection("iCHP")->find($filter, $options);
               $RunNo = 0;
               $runs = array();
               foreach ($result as $doc) \array_push($runs, $doc);

               if (\count($runs) > 0) $RunNo = (int)((object)$runs[0])->RunNo;
               $RunNo += 1;
     
               $data = [
                    "RunNo"               =>  $RunNo,
                    "EmployeeNumber"      =>  $EmployeeNumber,
                    "PetitionerName"      =>  $PetitionerName,
                    "PetitionerEmail"     =>  $PetitionerEmail,
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
                    "SatisfyScore"        =>  (int)$SatisfyScore,
                    "StatusID"            =>  null,
                    "ApprovalDT"          =>  null,
                    "ManagerRemarks"      =>  null,
                    "IsApprove"           =>  null,
                    "IsApproveOperator"   =>  null,
                    "OperatorApproveDT"   =>  null,
                    "OperatorRemarks"     =>  null,
                    "IsApproveSoftware"   =>  null,
                    "SoftwareApproveDT"   =>  null,
                    "SoftwareRemarks"     =>  null,
                    "InformationRequire"  =>  $InformationRequire,
                    "SendRequesttoManagerDT"   =>  null,
                    "SendRequesttoOperator"    =>  null,
                    "SendRequesttoSoftware"    =>  null,
                    "SendManagertoEmployeeDT"  =>  null,
                    "SendOperatortoEmployeeDT" =>  null,
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
                         'ManagerFullName' => 1, 
                         'ManagerEmail' => 1, 
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
                         "PetitionerEmail"     =>  $PetitionerEmail,
                         "SNCCompany"          =>  $SNCCompany,
                         "Department"          =>  $Department,
                         "Phone"               =>  $Phone,
                    ];
                    $this->dbiPMS->selectCollection("iCHP_Accounts")->insertOne($data2);
                } else { // update data
                    $filter = ["EmployeeNumber" => "$EmployeeNumber"];
                    $dataforUpdate = [
                         "PetitionerName"      =>  $PetitionerName,
                         "PetitionerEmail"     =>  $PetitionerEmail,
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
                    "ManagerRemarks"  =>  $ManagerRemarks,
                    "ApprovalDT"   =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
               ];

               $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
          
               // return $this->response->setJSON(["state" => true, "msg" => "Manager อนุมัติแล้ว"]);
               $pipeline = [
                    [
                    '$match' => [
                        '_id' => $this->MongoDBObjectId($RequestID)
                        ]
                    ],
                    ['$project' => [
                        '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 
                        'PetitionerName' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'OperatorRemarks' => 1, 'RunNo' => 1, 'ManagerFullName' => 1, 'ManagerRemarks' => 1
                        ]
                   ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);
               $PetitionerName = "";
               $WebsiteName = "";
               $RequestType = "";
               $ManagerRemarks = "";
               $RunNo = "";
               $ManagerFullName = "";

               foreach ($result as $doc) {
               $PetitionerName = $doc->PetitionerName;
               $WebsiteName = $doc->WebsiteName;
               $RequestType = $doc->RequestType;
               $ManagerRemarks = $doc->ManagerRemarks;
               $RunNo = $doc->RunNo;
               $ManagerFullName = $doc->ManagerFullName;
               };

               if ((boolean)$IsApprove) {         // ส่งไลน์
                    $this->lineNotify->sendMessage("

หมายเลขคำร้องขอที่: $RunNo
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

คุณ$ManagerFullName: อนุมัติคำร้องขอแล้ว
               ");      

               return $this->response->setJSON(["state" => true, "msg" => "Manager อนุมัติคำร้องขอแล้ว"]);

                    } else {
                    $this->lineNotify->sendMessage("
                    
หมายเลขคำร้องขอที่: $RunNo                   
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

คุณ$ManagerFullName: ไม่อนุมัติคำร้องขอ
เหตุผลที่ไม่อนุมัติ: $ManagerRemarks
               ");      
               return $this->response->setJSON(["state" => true, "msg" => "Manager ไม่อนุมัติคำร้องขอ"]);
                    }   
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
                    // "OperatorApproveDT"   =>  $this->MongoDBUTCDateTime(time()*1000),       เขียนแบบนี้ก็ได้
                ];

                $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);
           
                $pipeline = [
                    [
                    '$match' => [
                        '_id' => $this->MongoDBObjectId($RequestID)
                        ]
                    ],
                    ['$project' => [
                        '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 
                        'PetitionerName' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'OperatorRemarks' => 1, 'RunNo' => 1
                        ]
                   ]
               ];
               $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);
               $PetitionerName = "";
               $WebsiteName = "";
               $RequestType = "";
               $OperatorRemarks = "";
               $RunNo = "";

               foreach ($result as $doc) {
               $PetitionerName = $doc->PetitionerName;
               $WebsiteName = $doc->WebsiteName;
               $RequestType = $doc->RequestType;
               $OperatorRemarks = $doc->OperatorRemarks;
               $RunNo = $doc->RunNo;
               };

               if ((boolean)$IsApproveOperator) {         // ส่งไลน์
                    $this->lineNotify->sendMessage("

หมายเลขคำร้องขอที่: $RunNo
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

**ผู้จัดการแผนก CoDE อนุมัติคำร้องขอแล้ว**
               ");      

               return $this->response->setJSON(["state" => true, "msg" => "CoDE Manager อนุมัติคำร้องขอแล้ว"]);

                    } else {
                    $this->lineNotify->sendMessage("

หมายเลขคำร้องขอที่: $RunNo
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

**ผู้จัดการแผนก CoDE ไม่อนุมัติคำร้องขอ**
เหตุผลที่ไม่อนุมัติ: $OperatorRemarks
               ");      
               return $this->response->setJSON(["state" => true, "msg" => "CoDE Manager ไม่อนุมัติคำร้องขอ"]);

                    }   
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
               
                  $validate = is_null($RequestID) || is_null($IsApproveSoftware);
                  if ($validate) return $this->response->setStatusCode(400)->setJSON(["state" => false, "msg" => "กรอกข้อมูลไม่ครบถ้วน"]);
  
                 $now = new \DateTime();
 
                 $filter = ["_id" => $this->MongoDBObjectId($RequestID)];
                 $data = [
                     "IsApproveSoftware"   =>  (boolean)$IsApproveSoftware,
                     "SoftwareApproveDT"   =>  $this->MongoDBUTCDateTime($now->getTimestamp()*1000),
                     "SoftwareRemarks"     =>  $SoftwareRemarks,
                 ];

                 $this->dbiPMS->selectCollection("iCHP")->updateOne($filter, ['$set' => $data]);

                 $pipeline = [
                               [
                               '$match' => [
                                   '_id' => $this->MongoDBObjectId($RequestID)
                                   ]
                               ],
                               ['$project' => [
                                   '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 
                                   'PetitionerName' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'SoftwareRemarks' => 1, 'RunNo' => 1
                                   ]
                              ]
                          ];
                $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);
                $PetitionerName = "";
                $WebsiteName = "";
                $RequestType = "";
                $RunNo = "";
                $SoftwareRemarks = "";

                foreach ($result as $doc) {
                    $PetitionerName = $doc->PetitionerName;
                    $WebsiteName = $doc->WebsiteName;
                    $RequestType = $doc->RequestType;
                    $RunNo = $doc->RunNo;
                    $SoftwareRemarks = $doc->SoftwareRemarks;
                };

                 if ((boolean)$IsApproveSoftware) {         // ส่งไลน์
                     $this->lineNotify->sendMessage("

หมายเลขคำร้องขอที่: $RunNo
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

**คำร้องขอนี้ดำเนินการสำเร็จแล้ว**
               ");      

                     return $this->response->setJSON(["state" => true, "msg" => "Software ดำเนินการเสร็จแล้ว"]);

                 } else {
                    $this->lineNotify->sendMessage("

หมายเลขคำร้องขอที่: $RunNo
ชื่อผู้ยื่นคำร้อง: $PetitionerName
ระบบ: $WebsiteName
ประเภทคำร้อง: $RequestType

**คำร้องขอนี้ดำเนินการไม่สำเร็จ**
เหตุผล: $SoftwareRemarks
               ");      
                    return $this->response->setJSON(["state" => true, "msg" => "Software ไม่ดำเนินการ"]);

                 }        
            
                  
          } catch (\Exception $e) {
                  return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
      }  

     //TODO [GET] /Request/request-list
     // public function requestList()
     // {
     //      try {

     //           $pipeline = [
     //                ['$project' => ['_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'CreatedDT' => ['$dateToString' => ['date' => '$CreatedDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']], 
     //                'PetitionerName' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'OperationDate' => 1,  'IsApproveSoftware' => 1]]
     //           ];
     //           $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);

     //           $data = array();
     //           foreach ($result as $doc) array_push($data, $doc);

     //           return $this->response->setJSON($data);   
     //  }  catch (\Exception $e) {
     //           return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
     //      }
     // }


     //TODO [GET] /Request/score-list
     // public function scoreList()
     // {
     //      try {

     //           $pipeline = [
     //                     ['$project' => [
     //                          '_id' => 0, 'WebsiteName' => 1, 'SatisfyScore' => 1, 
     //                          ],
     //                     ]
     //           ];
     //           $result = $this->dbiPMS->selectCollection("iCHP")->aggregate($pipeline);

     //           $data = array();
     //           foreach ($result as $doc) array_push($data, $doc);

     //           return $this->response->setJSON($data);   
     //  }  catch (\Exception $e) {
     //           return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
     //      }
     // }


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
                                   '_id' => 0, 'RequestID' => ['$toString' => '$_id'], 'EmployeeNumber' => 1, 'PetitionerName' => 1, 'PetitionerEmail' => 1,
                                   'SNCCompany' => 1, 'Department' => 1, 'Phone' => 1, 'WebsiteName' => 1, 'RequestType' => 1, 'RequirementDetails' => 1, 
                                   'OperationDate' => 1, 'CreatedDT' => ['$dateToString' => ['date' => '$CreatedDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']], 
                                   'InformationRequire' => 1, 'ManagerFullName' => 1, 'ManagerEmail' => 1, 'IsApprove' => 1, 'ManagerRemarks' => 1, 
                                   'ApprovalDT' => ['$dateToString' => ['date' => '$ApprovalDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']],
                                   'IsApproveOperator' => 1, 'OperatorRemarks' => 1, 'IsApproveSoftware' => 1, 'SoftwareRemarks' => 1,  'SatisfyScore' => 1,
                                   'SoftwareApproveDT' => ['$dateToString' => ['date' => '$SoftwareApproveDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']],
                                   'OperatorApproveDT' => ['$dateToString' => ['date' => '$OperatorApproveDT', 'timezone' => 'Asia/Bangkok', 'format' => '%Y-%m-%d %H:%M:%S']],
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
          } catch (\Exception $e) {
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }


     //TODO [GET] /Request/snccompany-list
     public function sncCompanyList()
     {
          try {
               $pipeline = [
                    ['$project' => [
                         '_id' => 0, 
                         'CompanyID' => ['$toString' => '$_id'], 
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
               return $this->response->setJSON([]);
               return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
          }
     }


       //TODO [GET] /Request/employee-list
       public function employeeList()
       {
            try {
               $EmployeeNumber  = $this->request->getVar("EmployeeNumber");

                 $pipeline = [
                      [
                         '$match' => [
                              'EmployeeNumber' => "$EmployeeNumber"
                         ]
                         ],
                      ['$project' => [
                           '_id' => 0, 
                           'AccountID' => ['$toString' => '$_id'], 
                           'EmployeeNumber' => 1, 
                           'PetitionerName' => 1,
                           'PetitionerEmail' => 1,
                           'SNCCompany' => 1,
                           'Department' => 1,
                           'Phone' => 1,

                           ]
                      ]
                 ];
                 $result = $this->dbiPMS->selectCollection("iCHP_Accounts")->aggregate($pipeline);
  
                 $data = array();
                 foreach ($result as $doc) array_push($data, $doc);
  
               //   return $this->response->setJSON($data[0]);   
                 return $this->response->setJSON($data);   
       }  catch (\Exception $e) {
                 return $this->response->setJSON([]);
               //   return $this->response->setJSON(["state" => false, "msg" => $e->getMessage()]);
            }
       }



     
}
?>