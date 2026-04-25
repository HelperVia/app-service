<?php

namespace App\Services\Token;

use App\Contracts\Token\TokenInterface;
use App\DTO\Utils\JWTConfig;
use App\Utils\Crypto;
use App\Utils\JWTHelper;
use App\Traits\TokenCrypto;
use Exception;

class ConnectionTokenService implements TokenInterface
{
    use TokenCrypto;

    private $jwt = null;

    public function __construct(
        private Crypto $crypto
    ) {

        $config = JWTConfig::forAgent();
        $this->jwt = new JWTHelper($config);
    }

    public function encode(array $data): array
    {
        $this->validateEncodeData($data);

        $payload = [
            'agent_id' => $data['agent_id'],
            'company_id' => $data['company_id'],
            'device_type' => $data['device_type'],
            'license_id' => $data['license_id']
        ];
        return [
            'device_type' => $data['device_type'],
            'access_token' => $this->jwt->encode($payload),
            'expires_in' => $this->jwt->config->ttl
        ];
    }

    public function decode(string $token): array
    {
        if (empty($token)) {
            throw new Exception("Missing token", 400);
        }

        $decoded = $this->jwt->decode($token);

        $this->validateDecodedToken($decoded);

        return $decoded;
    }

    public function validateEncodeData(array $data): void
    {
        if (!isset($data['company_id'], $data['agent_id'], $data['device_type'], $data['license_id'])) {
            throw new Exception("Missing required token fields", 400);
        }
    }

    private function validateDecodedToken(array $decoded): void
    {
        if (!isset($decoded['data'])) {
            throw new Exception("Invalid token format", 400);
        }

        if (isset($decoded['exp']) && $decoded['exp'] < time()) {
            throw new Exception("Token expired", 401);
        }
    }





}