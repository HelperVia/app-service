<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Companies extends Model
{


    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();  // UUID ata
            }
        });
    }
    protected $table = 'companies';
    protected $fillable = [
        'company_name',
        'license_number',
        'create_step',
        'status'
    ];

    public function invites()
    {
        return $this->hasMany(Invite::class, 'inviting_company_id', 'id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }
}
