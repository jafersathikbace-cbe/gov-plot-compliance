<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Submission extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['allotment_case_id','milestone_id','submitted_by','investment_spent','employees_hired','construction_status','remarks','evidence_files','status','reviewed_by','review_remarks','submitted_at','reviewed_at'];
}
