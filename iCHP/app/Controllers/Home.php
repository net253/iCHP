<?php

namespace App\Controllers;

// use App\Libraries\PHPSendMail;

class Home extends BaseController
{
    public function index()
    {
        // try {
        //     $mailer = new PHPSendMail();
        //     $mailer->setNameMailer('TEST 2');
        //     $result = $mailer->sendMail('pongponk555@gmail.com', 'Subject', '<h1>Hello World10</h1>');
        //     return $this->response->setJSON($result);
        // } catch (\Exception $e) {
        //     return "Error: " . $e->getMessage();
        // }
        return $this->response->setJSON(["msg" => "Welcome to iCHP"]);
    }
}
