<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $table = "short_links";
    protected $fillable = ['short_token', 'target', 'type'];
    public function invite()
    {

        return $this->belongsTo(Invite::class, 'target', 'invite_code')
            ->where('type', 'invite');
    }
}
