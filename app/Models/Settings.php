<?php

namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;
use App\Traits\MongoMultiTenantSystem;
class Settings extends Model
{

    use MongoMultiTenantSystem;

    public $tenant;
    protected $connection = 'mongodb';
    protected $table = 'settings';
    protected $fillable = ['id', 'prechatform', 'postchatform', 'widget', 'widget.customization', 'widget.languages'];

}