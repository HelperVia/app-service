<?php

namespace App\Http\Controllers;

use App\Actions\App\Connection\Token\CreateConnectionTokenAction;
use App\Domain\Department\Services\DepartmentService;
use App\Http\Requests\Init\ConnectionTokenRequest;
use App\Http\Resources\Agent\AgentsResource;
use App\Domain\Settings\Services\SettingService;
use App\Http\Resources\Department\TeamDepartmentResource;
use App\Http\Resources\User\AccountDataResource;
use App\Services\UserService;
use Cache;
use App\Http\Resources\Widget\Setting\SettingResource;
use App\Services\CompanyService;


class InitController extends Controller
{

    public function appToken(
        ConnectionTokenRequest $request,
        CreateConnectionTokenAction $action,
    ) {

        $validated = $request->validated();
        $device_type = $validated['device_type'];
        $cookie_name = $validated['cookie_name'];
        $user = auth()->user();

        return $action->execute($device_type, [
            'company_id' => $user->company->id,
            'license_id' => $user->license,
            'agent_id' => $user->agent_id,
            'cookie_name' => $cookie_name
        ]);


    }
    public function appInit(

        UserService $userService,
        SettingService $settingService,
        CompanyService $companyService,
        DepartmentService $departmentService
    ) {

        $user = auth()->user();


        $settingsArray = Cache::rememberForever($user->license . ':settings', function () use ($user, $settingService) {
            return $settingService->getAllSettings($user->license)->toArray();
        });


        $companies = $userService->getCompanies($user);
        $settings = SettingResource::make((object) $settingsArray)->resolve();
        $agents = AgentsResource::collection($companyService->getTeams($user->company));
        $departments = TeamDepartmentResource::collection(collect($departmentService->getWithAgents($user->license)));
        $account = new AccountDataResource($user);
        $settings['widget_default_languages'] = config("settings.widget_default_languages");
        return response()->success([
            'companies' => $companies,
            'teams' => [
                'agents' => $agents,
                'departments' => $departments
            ],
            'settings' => $settings,
            'account' => $account,
            'connection_cookie_name' => 'hv:' . $user->license . ':connection-token',
            'license_number' => $user->license
        ]);

    }

    public function initialize()
    {
        return response()->success();
    }
}
