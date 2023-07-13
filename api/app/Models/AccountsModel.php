<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountsModel extends Model
{
     protected $table = 'Accounts';

     protected $primaryKey = 'AccountID';

     protected $allowedFields = [
          "FullNameEN",
          "FullNameTH",
          "Username",
          "Password",
          "Email",
          "Role",
          "LoginDT",
     ];
}
