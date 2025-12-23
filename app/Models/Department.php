<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\MongoMultiTenantSystem;
class Department extends Model
{

    use MongoMultiTenantSystem;

    protected $connection = 'mongodb';
    protected $table = 'departments';
    protected $fillable = ['company_license_number', 'department_name', 'default'];

}