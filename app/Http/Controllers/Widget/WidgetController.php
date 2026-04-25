<?php

namespace App\Http\Controllers\Widget;

use App\Domain\Settings\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Widget\WidgetRequest;
use App\Http\Resources\Widget\Setting\SettingResource;
use App\Models\Settings;
use App\Services\CompanyService;
use Cache;


class WidgetController extends Controller
{

    public function __construct(private readonly SettingService $settingService)
    {
    }
    public function bootstrap(WidgetRequest $request)
    {
        $callback = $request->input('callback');
        $company = $request->input('company');
        $license_id = $request->input('license_id');




        $settingsArray = Cache::rememberForever($license_id . ':settings', function () use ($license_id) {
            return $this->settingService->getAllSettings($license_id)->toArray();
        });

        $response_data = array_merge(
            ['company_id' => $company->id],
            SettingResource::make((object) $settingsArray)->resolve()
        );

        return $this->jsonpResponse($callback, $response_data);


    }
    private function jsonpResponse(string $callback, array $data, int $status = 200)
    {

        return response()
            ->json($data)
            ->withCallback($callback)
            ->header('Content-Type', 'application/javascript; charset=utf-8')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'DENY')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
