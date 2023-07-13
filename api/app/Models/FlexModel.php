<?php

namespace App\Models;

use CodeIgniter\Model;

class FlexModel extends Model
{
     protected $table = 'OpenTable';

     protected $primaryKey = 'ID';

     protected $allowedFields = [
          "Name",
     ];
}
