<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Repositories\CompanyRepository;
use App\Services\CompanyService;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function __construct(private UserService $userService, private CompanyService $companyService)
    {
    }
    public function handle(Request $request, Closure $next): Response
    {
        $license = $request->header('X-License');

        $user = auth()->user();

        if (!$this->userService->isValidLicense($license, $user)) {

            return response()->json(
                [
                    'message' => "License header missing",
                    'step' => 'companies',
                ],
                403
            );
        }

        $company = $this->companyService->findCompanyByLicenseNumber($license);

        if (empty($company)) {

            return response()->json(
                [
                    'message' => "Invalid company",
                    'step' => 'companies',
                ],
                403
            );
        }
        $user->setAttribute('company', $company);
        $user->setAttribute('license', $license);
        return $next($request);


    }
}
