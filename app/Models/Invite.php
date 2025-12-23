<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Invite extends Model
{



    protected $table = 'invite';
    protected $fillable = [
        'inviting_company_id',
        'invited_email',
        'invited_id',
        'inviting_user',
        'status',
        'invite_expire',
        'invited_role',
        'temporary_name',
        'invite_code',

    ];

    public function company()
    {
        return $this->belongsTo(Companies::class, 'inviting_company_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'invited_id', 'id');
    }
    public function shortLink()
    {

        return $this->hasOne(ShortLink::class, 'target', 'invite_code')
            ->where('type', 'invite');
    }
}
