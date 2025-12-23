<?php

namespace App\Services\Token;

use App\Contracts\Token\TokenInterface;
use App\Utils\Crypto;
use App\Traits\TokenCrypto;
use InvalidArgumentException;

class InviteTokenService implements TokenInterface
{

    use TokenCrypto;

    public function __construct(

        private readonly Crypto $crypto
    ) {
    }

    public function encode(array $data): string
    {
        $this->validateEncodeData($data);
        $json = $this->encodeJson($data);
        $encrypted = $this->encryptData($json);
        return $this->base64Encode($encrypted);

    }

    public function decode(string $token): array
    {
        if (empty($token)) {
            throw new InvalidArgumentException("Missing token", 400);
        }

        $base64Data = $this->base64Decode($token);
        $decrypted = $this->decryptData($base64Data);
        return $this->decodeJson($decrypted);
    }

    public function validateEncodeData(array $data): void
    {
        if (!isset($data['email'], $data['inviting_company_id'])) {
            throw new InvalidArgumentException("Missing required token fields", 400);
        }
    }

    private function base64Encode(string $data): string
    {

        return base64_encode($data);

    }

    private function base64Decode(string $data): string
    {
        $base64Decode = base64_decode($data, true);
        if ($base64Decode === false) {
            throw new InvalidArgumentException("Invalid token format (base64)");
        }

        return $base64Decode;
    }


}