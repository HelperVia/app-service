<?php

namespace App\Actions\Auth;

use App\Http\Resources\Auth\AuthResource;
use App\Models\User;

class AuthDataAction
{

    public function execute(User $user, string $token)
    {

        $user->accessToken = $token;
        return new AuthResource($user, $token);

    }


}