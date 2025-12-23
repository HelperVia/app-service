<?php

namespace App\Actions\Auth;

use App\Exceptions\ApiException;
use App\Services\Verification\EmailVerificationService;

class VerifyEmailAction
{


    public function __construct(private EmailVerificationService $service)
    {
    }
    public function execute(array $params)
    {

        $code = $params['code'] ?? null;
        if (!$this->service->isValid($code)) {
            throw new ApiException("Invalid or expired verification code.", 400);
        }

        $verify = $this->service->markAsVerified();
        if (empty($verify)) {
            throw new ApiException("Invalid or expired verification code.", 400);
        }

    }
}