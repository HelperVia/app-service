<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Rules\ValidLicenseId;
use App\Services\CompanyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidWidgetBootstrap
{

    public function __construct(private readonly CompanyService $companyService)
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $licenseId = $request->route('license_id');
        $validator = new ValidLicenseId;
        $validator->validate('license_id', $licenseId, function ($message) {
            throw new ApiException('Invalid License Number', code: 403);
        });
        $company = $this->companyService->isValidLicense($licenseId);
        if (empty($company)) {
            throw new ApiException('Invalid License Number', 403);
        }

        $request->merge(['company' => $company]);
        return $next($request);
    }
}
