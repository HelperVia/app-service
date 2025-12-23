<?php


namespace App\Services\Verification;

use App\Constants\YesNo;
use App\Contracts\Verification\VerificationInterface;
use App\Models\User;
use App\Services\UserService;

class EmailVerificationService implements VerificationInterface
{

    public function __construct(private UserService $service)
    {
    }
    public function isValid(string $code, ?User $user = null): bool
    {
        $user = empty($user) ? $this->service->getAuthenticatedUser() : $user;

        if ($user->email_verification_code == $code && $user->email_verification == YesNo::NO) {
            return true;
        }

        return false;
    }

    public function generate(): string
    {

        return rand(1111, 9999);
    }

    public function markAsVerified(?User $user = null): ?User
    {

        $user = empty($user) ? $this->service->getAuthenticatedUser() : $user;
        return $this->service->update($user, [
            'email_verification' => YesNo::YES
        ]);
    }

}