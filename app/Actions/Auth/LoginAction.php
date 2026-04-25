<?php

namespace App\Actions\Auth;

use App\Exceptions\ApiException;
use Auth;
use Throwable;
class LoginAction
{


    public function __construct(
        private readonly CreateTokenAction $action,
        private readonly AuthDataAction $authDataAction
    ) {
    }
    public function execute(array $request): \Illuminate\Http\Resources\Json\JsonResource|bool
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
            return $this->authDataAction->execute($user, $token['access_token']);

        }
        return false;




    }
}