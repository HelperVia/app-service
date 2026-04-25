<?php

namespace App\Domain\Customer\Actions;

use App\Exceptions\ApiException;
use App\Domain\Customer\Actions\RefreshTokenAction;
class CreateTokenAction
{

    public function __construct(
        private readonly AccessTokenAction $accessTokenAction,
        private readonly GenerateUserIdAction $generateUserIdAction,
        private readonly RefreshTokenAction $refreshTokenAction
    ) {
    }

    public function execute(array $data, $refreshToken = null)
    {
        $this->validate($data);

        $customerId = $this->decodeRefreshToken($refreshToken);


        if (!$customerId) {
            $customerId = $this->generateUserIdAction->generate();
        }

        $data['customer_id'] = $customerId;

        $crt = $this->createRefreshToken($data);
        $access_token = $this->accessTokenAction->create($data);


        return [
            'access_token' => $access_token,
            'refresh_token' => $crt,
            'customer_id' => $data['customer_id'],
            'company_id' => $data['company_id'],
            'expires_in' => config('jwt.customer.ttl')
        ];


    }


    private function createRefreshToken(array $data)
    {
        return $this->refreshTokenAction->encode([
            'company_id' => $data['company_id'],
            'customer_id' => $data['customer_id']
        ]);


    }
    private function decodeRefreshToken(string $refreshToken = null): string|bool
    {
        if (empty($refreshToken))
            return false;
        $decoded = $this->refreshTokenAction->decode($refreshToken);


        if (isset($decoded['customer_id']))
            return $decoded['customer_id'];

        return false;
    }

    private function validate(array &$data)
    {

        $data['grant_type'] ??= 'cookie';
        $data['response_type'] ??= 'token';
        $data['customer_id'] ??= time();
        if (empty($data['company_id'])) {
            throw new ApiException('Invalid Company ID');
        }

        return $data;
    }
}