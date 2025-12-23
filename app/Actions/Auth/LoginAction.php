<?php

namespace App\Actions\Auth;

use App\Exceptions\ApiException;
use Auth;
use Throwable;
class LoginAction
{


    public function __construct(private CreateTokenAction $action)
    {
    }
    public function execute(array $request): array|bool
    {


        $email = $request['email'];
        $password = $request['password'];

        $response = Auth::attempt([
            'email' => $email,
            'password' => $password
        ]);

        if ($response) {
            $user = auth()->user();
            $token = $this->action->execute($user);

            return [
                'id' => $user->id,
                'fullname' => $user->full_name,
                'email' => $user->email,
                'accessToken' => $token['access_token'],

            ];

        }
        return false;




    }
}