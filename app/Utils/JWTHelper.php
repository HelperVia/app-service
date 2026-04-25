<?php

namespace App\Utils;

use App\DTO\Utils\JWTConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
class JWTHelper
{

    public function __construct(
        public JWTConfig $config
    ) {

    }

    public function encode(array $data): string
    {
        $payload = [
            ...$data,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $this->config->ttl,
        ];

        if ($this->config->iss) {
            $payload['iss'] = $this->config->iss;
        }


        return JWT::encode($payload, $this->config->secretKey, $this->config->algo);
    }

    public function decode(string $jwt): array
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->config->secretKey, $this->config->algo));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception("Invalid Token: " . $e->getMessage());
        }
    }

}