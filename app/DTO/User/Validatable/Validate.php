<?php

namespace App\DTO\User\Validatable;

use App\Constants\YesNo;
class Validate
{




    protected function validateEmailVerification($email_verification)
    {
        $validEmailVerification = [
            YesNo::YES,
            YesNo::NO
        ];

        if (!in_array($email_verification, $validEmailVerification, true)) {
            throw new \InvalidArgumentException(
                "Invalid email verification status '{$email_verification}'. Valid statuses: " . implode(", ", $validEmailVerification)
            );
        }
    }

}