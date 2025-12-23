<?php

namespace App\DTO\User;

use App\Contracts\DTO\DtoInterface;
use App\DTO\User\Validatable\Validate;
use App\Traits\DtoToArray;
class UpdateUserData extends Validate implements DtoInterface
{

    use DtoToArray;
    public function __construct(
        private ?string $email = null,
        private ?string $email_verification = null,
        private ?string $email_verification_code = null,
        private ?string $password = null,
        private ?string $full_name = null,

    ) {
        if ($this->full_name !== null) {
            $this->full_name = ucwords(strtolower($this->full_name));
        }

        $this->validate();

    }

    public function validate(): void
    {
        if ($this->email_verification !== null) {
            $this->validateEmailVerification($this->email_verification);
        }

    }







}