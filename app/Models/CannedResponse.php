<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\MongoMultiTenantSystem;
class CannedResponse extends Model
{

    use MongoMultiTenantSystem;

    protected $connection = 'mongodb';
    protected $table = 'canned_responses';
    protected $fillable = ['shortcut', 'message', 'administrator'];

}