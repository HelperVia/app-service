<?php

namespace App\Actions\Auth;

use App\Models\User;

class CreateTokenAction
{


    public function execute(User $user, string $deviceType = 'web'): array
    {

        $result = $user->createToken($deviceType);


        return [
            'access_token' => $result->accessToken,
            'token_type' => 'Bearer'
        ];
    }
}