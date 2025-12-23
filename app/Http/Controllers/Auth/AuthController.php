<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    public function __construct(private readonly LoginAction $loginAction)
    {
    }
    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [], [
            'email' => 'Email Adress',
            'password' => 'Password'
        ]);


        try {
            $response = $this->loginAction->execute($validated);

            if (!$response) {
                throw new ApiException("The email or password is incorrect", 401);
            }
            return response()->success($response);


        } catch (Throwable $e) {
            throw $e;
        }



    }

    public function logout(LogoutAction $action)
    {

        try {
            $action->execute(auth()->user());

            return response()->success([], 'Logged out successfully');

        } catch (Throwable $e) {
            throw $e;
        }

    }
}
