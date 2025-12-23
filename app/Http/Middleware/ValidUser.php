<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Constants\YesNo;
class ValidUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (empty($user->full_name)) {
            return response()->json([
                'step' => 'settings',
            ], 403);
        }
        if ($user->email_verification == YesNo::NO) {
            return response()->json([
                'step' => 'email_verify',
            ], 403);
        }

        return $next($request);
    }
}
