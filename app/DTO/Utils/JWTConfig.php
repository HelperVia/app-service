<?php

namespace App\DTO\Utils;

class JWTConfig
{
    public function __construct(
        public readonly string $secretKey,
        public readonly int $ttl,
        public readonly string $iss = '',
        public readonly string $algo = 'HS256'
    ) {
    }



    public static function forAgent(): self
    {
        return new self(
            secretKey: config('jwt.agent.key'),
            ttl: config('jwt.agent.ttl'),
            iss: config('jwt.agent.iss')
        );
    }
}