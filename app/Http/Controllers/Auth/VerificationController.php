<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\VerifyEmailAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyEmail(Request $request, VerifyEmailAction $action)
    {

        $validated = $request->validate([
            'code' => 'required|digits:4'
        ], [], [
            'code' => 'Email Code'
        ]);

        $action->execute($validated);

        return response()->success(message: 'Email verification successful');

    }
}
