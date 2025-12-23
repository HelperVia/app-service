<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\SignupAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    public function __construct(private readonly SignupAction $action)
    {

    }
    public function register(Request $request)
    {

        $validated = $request->validate([
            "email" => "required|email",
            "fullname" => "required|min:5|max:25",
            "password" => "required|min:6",
            "invite_code" => "sometimes|string"
        ], [], [
            "email" => "Business Email",
            "password" => "Password",
            "fullname" => "Fullname",
            "invite_code" => "Invite Code"
        ]);

        $response = $this->action->execute($validated);

        return response()->success([
            'token' => $response['token'] ?? [],
            'user' => [
                'id' => $response['user']->id,
                'name' => $response['user']->full_name,
                'email' => $response['user']->email
            ]
        ], 'Account created successfully.');
    }
}
