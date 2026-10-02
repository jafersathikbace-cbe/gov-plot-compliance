<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Plot extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['plot_number','area','lease_duration_years','project_type','required_investment','employment_commitment','construction_deadline','financial_conditions','status'];
}
