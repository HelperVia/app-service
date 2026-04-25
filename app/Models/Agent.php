<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\MongoMultiTenantSystem;
class Agent extends Model
{

    use MongoMultiTenantSystem;

    protected $connection = 'mongodb';
    protected $table = 'agents';
    protected $fillable = ['socket', 'license_number', 'user_id', 'status', 'away', 'active_chat', 'chat_limit', 'department_ids', 'auto_assign', 'role', 'agent_name', 'job_title'];

}