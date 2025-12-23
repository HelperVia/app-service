<?php

namespace App\DTO\User;
use App\Constants\YesNo;
use App\Contracts\DTO\DtoInterface;
use Illuminate\Support\Facades\Hash;
use App\Services\Verification\EmailVerificationService;
use App\Traits\DtoToArray;
use App\DTO\User\Validatable\Validate;
class CreateUserData extends Validate implements DtoInterface
{

    use DtoToArray;
    public function __construct(
        private readonly string $email,
        private readonly string $email_verification,

        private ?string $email_verification_code = null,
        private ?string $password = null,
        private ?string $full_name = null,

    ) {
        $this->password ??= $this->generatePassword();
        $this->password = Hash::make($this->password);
        if ($this->email_verification == YesNo::YES && $this->email_verification_code == null) {
            $this->email_verification_code = app(EmailVerificationService::class)->generate();
        }
        if ($this->full_name !== null) {
            $this->full_name = ucwords(strtolower($this->full_name));
        }

    }

    public function validate(): void
    {
        $this->validateEmailVerification($this->email_verification);
    }





    private function generatePassword($length = 10)
    {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+';
        $all = $lower . $upper . $numbers . $special;

        $password = '';
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);

    }

}