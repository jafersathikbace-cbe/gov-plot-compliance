<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class IncentiveClaim extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['allotment_case_id','type','amount','status','submitted_by','reviewed_by','remarks','payment_reference','approved_at','paid_at'];
}
