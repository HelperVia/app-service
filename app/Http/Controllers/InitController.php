<?php

namespace App\Http\Controllers;

use App\Constants\YesNo;
use App\Http\Resources\Agent\AgentsResource;
use App\Http\Resources\Company\UsersResource;
use App\Http\Resources\User\AccountDataResource;
use App\Models\User;
use App\Services\CompanyService;
use App\Utils\Crypto;
use App\Services\Token\ConnectionTokenService;
use App\Services\UserService;
use Illuminate\Http\Request;

class InitController extends Controller
{
    public function appInit(ConnectionTokenService $tokenService, CompanyService $companyService)
    {

        $user = auth()->user();
        $data = $tokenService->encode([
            'license' => $user->license,
            'type' => 'operators',
            'user_id' => $user->id
        ]);


        $teams = AgentsResource::collection($companyService->getTeams($user->company));

        $account = new AccountDataResource($user);

        return response()->success([
            'teams' => [
                'agents' => $teams
            ],
            'account' => $account,
            'token' => $data,
            'license_number' => $user->license
        ]);
    }

    public function initialize()
    {
        return response()->success();
    }
}
