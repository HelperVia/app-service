<?php

namespace App\Contracts\Token;

interface TokenInterface
{

    public function encode(array $data): string;
    public function decode(string $token): array;
    public function validateEncodeData(array $data);
}