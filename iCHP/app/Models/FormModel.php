<?php

namespace App\Models;

use CodeIgniter\Model;

class FormModel extends Model
{
     protected $table = 'Form';

     protected $primaryKey = 'FormID';

     protected $allowedFields = [
         "FormNo"
        ,"FormName"
        ,"Topic1"
        ,"Topic2"
        ,"Topic3"
        ,"Topic4"
        ,"Topic5"
        ,"Topic6"
        ,"Topic7"
        ,"Topic8"
        ,"Topic9"
        ,"Topic10"
        ,"Topic11"
        ,"Topic12"
        ,"Topic13"
        ,"Topic14"
        ,"Topic15"
        ,"Topic16"
        ,"Topic17"
        ,"Topic18"
        ,"Topic19"
        ,"Topic20"
        ,"Topic21"
        ,"Topic22"
        ,"Topic23"
        ,"Topic24"
        ,"Topic25"
        ,"SubTopic1"
        ,"SubTopic2"
        ,"SubTopic3"
        ,"SubTopic4"
        ,"SubTopic5"
        ,"SubTopic6"
        ,"SubTopic7"
        ,"SubTopic8"
        ,"SubTopic9"
        ,"SubTopic10"
        ,"SubTopic11"
        ,"SubTopic12"
        ,"SubTopic13"
        ,"SubTopic14"
        ,"SubTopic15"
        ,"SubTopic16"
        ,"SubTopic17"
        ,"SubTopic18"
        ,"SubTopic19"
        ,"SubTopic20"
        ,"SubTopic21"
        ,"SubTopic22"
        ,"SubTopic23"
        ,"SubTopic24"
        ,"SubTopic25"
        ,"IsDraft"
        ,"CreatorID"
        ,"CreatedDT"
        ,"ReleasedDT" 
     ];
}
