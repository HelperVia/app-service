<?php

namespace App\Services\Token;

use App\Contracts\Token\TokenInterface;
use App\Utils\Crypto;
use App\Utils\JWTHelper;
use App\Traits\TokenCrypto;
use Exception;

class ConnectionTokenService implements TokenInterface
{
    use TokenCrypto;
    private const TOKEN_LIFETIME = 3600;

    public function __construct(
        private JWTHelper $jwt,
        private Crypto $crypto
    ) {
    }

    public function encode(array $data): string
    {
        $this->validateEncodeData($data);

        $tokenData = [
            'license' => $data['license'],
            'type' => $data['type'],
            'user_id' => $data['user_id'],
        ];

        $json = $this->encodeJson($tokenData);
        $encrypted = $this->encryptData($json);

        $payload = [
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + self::TOKEN_LIFETIME,
            'data' => $encrypted
        ];

        return $this->jwt->encode($payload);
    }

    public function decode(string $token): array
    {
        if (empty($token)) {
            throw new Exception("Missing token", 400);
        }

        $decoded = $this->jwt->decode($token);

        $this->validateDecodedToken($decoded);

        $json = $this->decryptData($decoded['data']);
        $payload = $this->decodeJson($json);

        $this->validatePayloadStructure($payload);

        return $payload;
    }

    public function validateEncodeData(array $data): void
    {
        if (!isset($data['license'], $data['type'], $data['user_id'])) {
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

    private function validatePayloadStructure(array $payload): void
    {
        if (!isset($payload['license'], $payload['type'], $payload['user_id'])) {
            throw new Exception("Token missing required fields", 400);
        }
    }




}