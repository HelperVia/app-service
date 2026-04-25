<?php

namespace App\Domain\Customer\Actions;

use Str;

class GenerateUserIdAction
{
    public function generate()
    {
        return (string) Str::uuid();
    }
}