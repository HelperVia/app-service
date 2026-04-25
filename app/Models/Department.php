<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\MongoMultiTenantSystem;
class Department extends Model
{

    use MongoMultiTenantSystem;

    protected $connection = 'mongodb';
    protected $table = 'departments';
    protected $fillable = ['department_name', 'default', 'status', 'department_key'];

}