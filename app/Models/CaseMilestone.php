<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CaseMilestone extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['allotment_case_id','type','title','description','due_date','required_value','current_value','unit','status','verified_at','verified_by','warning_sent_at','overdue_alert_sent_at','evidence_required'];
}
