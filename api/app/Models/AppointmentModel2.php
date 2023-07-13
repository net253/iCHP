<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel2 extends Model
{
     protected $table = 'Appointments2';

     protected $primaryKey = 'AppointmentID';
     protected $allowedFields = [
               "FormNo",
               "FormName",
               "VendorCode",
               "QualitySystem",
               "CompanyName",
               "Province",
               "District",
               "CompanySNC",
               "VendorName",
               "VendorEmail",
               "EmailNotifications",
               "LeaderID",
               "StartDate",
               "EndDate",
               "ConfirmStatus",
               "ConfirmDT",
               "TotalScore",
               "IsPass",
               "MaxScore",
               "CreatorID",
               "AssessorID1",
               "AssessorID2",
               "AssessorID3",
               "AssessorID4",
               "AssessorID5",
               "AssessorID6",
               "AssessorID7",
               "AssessorID8",
               "AssessorID9",
               "AssessorID10",
               "AssessorID11",
               "AssessorID12",
               "AssessorID13",
               "AssessorID14",
               "AssessorID15",
               "AssessorID16",
               "AssessorID17",
               "AssessorID18",
               "AssessorID19",
               "AssessorID20",
               "AssessorID21",
               "AssessorID22",
               "AssessorID23",
               "AssessorID24",
               "AssessorID25",
               "AssessorIDConfirmed1",
               "AssessorIDConfirmed2",
               "AssessorIDConfirmed3",
               "AssessorIDConfirmed4",
               "AssessorIDConfirmed5",
               "AssessorIDConfirmed6",
               "AssessorIDConfirmed7",
               "AssessorIDConfirmed8",
               "AssessorIDConfirmed9",
               "AssessorIDConfirmed10",
               "AssessorIDConfirmed11",
               "AssessorIDConfirmed12",
               "AssessorIDConfirmed13",
               "AssessorIDConfirmed14",
               "AssessorIDConfirmed15",
               "AssessorIDConfirmed16",
               "AssessorIDConfirmed17",
               "AssessorIDConfirmed18",
               "AssessorIDConfirmed19",
               "AssessorIDConfirmed20",
               "AssessorIDConfirmed21",
               "AssessorIDConfirmed22",
               "AssessorIDConfirmed23",
               "AssessorIDConfirmed24",
               "AssessorIDConfirmed25",
               "PicUpload1",
               "PicUpload2",
               "PicUpload3",
               "PicUpload4",
               "PicUpload5",
               "CreateDT",
               "Topic1",
               "Topic2",
               "Topic3",
               "Topic4",
               "Topic5",
               "Topic6",
               "Topic7",
               "Topic8",
               "Topic9",
               "Topic10",
               "Topic11",
               "Topic12",
               "Topic13",
               "Topic14",
               "Topic15",
               "Topic16",
               "Topic17",
               "Topic18",
               "Topic19",
               "Topic20",
               "Topic21",
               "Topic22",
               "Topic23",
               "Topic24",
               "Topic25",
               "SubTopic1",
               "SubTopic2",
               "SubTopic3",
               "SubTopic4",
               "SubTopic5",
               "SubTopic6",
               "SubTopic7",
               "SubTopic8",
               "SubTopic9",
               "SubTopic10",
               "SubTopic11",
               "SubTopic12",
               "SubTopic13",
               "SubTopic14",
               "SubTopic15",
               "SubTopic16",
               "SubTopic17",
               "SubTopic18",
               "SubTopic19",
               "SubTopic20",
               "SubTopic21",
               "SubTopic22",
               "SubTopic23",
               "SubTopic24",
               "SubTopic25",
               "Note",
               "AssessmentConfirmDT",
               "AssessmentParticipants",
               "SuggestionMain",
               "Suggestion1",
               "Suggestion2",
               "Suggestion3",
               "Suggestion4",
               "Suggestion5",
               "Suggestion6",
               "Suggestion7",
               "Suggestion8",
               "Suggestion9",
               "Suggestion10",
               "Suggestion11",
               "Suggestion12",
               "Suggestion13",
               "Suggestion14",
               "Suggestion15",
               "Suggestion16",
               "Suggestion17",
               "Suggestion18",
               "Suggestion19",
               "Suggestion20",
               "Suggestion21",
               "Suggestion22",
               "Suggestion23",
               "Suggestion24",
               "Suggestion25",
               "NotifyID",
               "Subject",
               "TemplateNo",
               "DayAhead",
               "DayCount",
               "IsSent",
               "FormID",
               "SentAppointmentToSupplierDT",
               "SentAppointmentToAssessorDT",
               "SendNotifyDT1",
               "SendNotifyDT2",
               "SentAssessmentToSupplierDT",
          
     ];
}