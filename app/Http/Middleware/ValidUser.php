<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
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
            throw new ApiException(
                "First name and last name cannot be null.",
                403,
                ["MissingFullName"]
            );
        }
        if ($user->email_verification == YesNo::NO) {

            throw new ApiException(
                "Your email address is not verified. Please verify your email to proceed.",
                403,
                ["EmailNotVerified"]
            );

        }

        return $next($request);
    }
}
