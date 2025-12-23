<?php
namespace App\Contracts\Verification;

use App\Models\User;
interface VerificationInterface
{
    public function isValid(string $code): bool;

    public function generate(): string;
    public function markAsVerified(?User $user): ?User;
}