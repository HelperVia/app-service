<?php

namespace App\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
class JWTHelper
{
    private string $key;
    private string $algo = 'HS256';

    public function __construct()
    {
        $this->key = config('app.jwt_secret', env('JWT_SECRET'));
    }

    public function encode(array $payload): string
    {

        return JWT::encode($payload, $this->key, $this->algo);
    }

    public function decode(string $jwt): array
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->key, $this->algo));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception("Token geçersiz: " . $e->getMessage());
        }
    }

}