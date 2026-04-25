<?php

namespace App\Actions\App\Connection\Token;

use App\Services\Token\ConnectionTokenService;

class CreateConnectionTokenAction
{



    public function __construct(private readonly ConnectionTokenService $tokenService)
    {

    }

    public function execute(string $device_type = 'mobile', array $data)
    {

        $data = (object) $data;
        $connection_data = $this->tokenService->encode([
            'device_type' => $device_type,
            'agent_id' => $data->agent_id,
            'company_id' => $data->company_id,
            'license_id' => $data->license_id
        ]);


        if ($device_type == 'browser') {
            return $this->browser($connection_data, $data->cookie_name);
        }

        return $this->other($connection_data);

    }

    private function browser(array $data, string $cookie)
    {
        return response()->success($this->successData($data))
            ->cookie(
                $cookie,
                $data['access_token'],
                ceil($data['expires_in'] / 60),
                '/',
                '.helpervia.com',
                true,
                true,
                false,
                'Strict'
            );
    }

    private function other($data)
    {
        return response()->success($this->successData($data));
    }

    private function successData(array $data): array
    {
        return [
            'device_type' => $data['device_type'],
            'access_token' => $data['access_token'],
            'expires_in' => $data['expires_in']
        ];
    }


}