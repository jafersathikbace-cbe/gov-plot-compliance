<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AllotmentCase extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['plot_id','allottee_user_id','policy_template_id','title','status','investment_required','employment_required','construction_deadline','subsidy_amount','caution_deposit_amount','documents','district_officer_id','inspection_officer_id','timeline_generated_at','activated_at','closed_at','legal_status','is_archived'];
}
