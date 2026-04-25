<?php

namespace App\Domain\Customer\Actions;


use App\Services\Token\Customer\RefreshTokenService;
use Throwable;

class RefreshTokenAction
{


    public function __construct(private readonly RefreshTokenService $refreshService)
    {
    }
    public function decode(string $refreshToken): array
    {
        try {
            return $this->refreshService->decode($refreshToken);
        } catch (Throwable $e) {
            return [];
        }

    }

    public function encode(array $data): string
    {
        return $this->refreshService->encode($data);
    }
}