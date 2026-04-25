<?php

namespace App\Domain\Customer\Actions;

use App\Services\Token\Customer\AccessTokenService;

class AccessTokenAction
{


    public function __construct(private readonly AccessTokenService $tokenService)
    {
    }

    public function create(array $data)
    {
        return $this->tokenService->encode($data);

    }

}