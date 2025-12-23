<?php
namespace App\Actions\Auth;

use App\Models\User;

class LogoutAction
{


    public function execute(User $user)
    {
        return $user->token()->revoke();
    }
}