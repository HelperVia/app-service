<?php

namespace App\Traits;
use Exception;
trait TokenCrypto
{
    public function encryptData(string $data): string
    {
        try {
            return $this->crypto->data($data)->encrypt();
        } catch (Exception $e) {
            throw new Exception("Failed to encrypt token", 500);
        }
    }

    public function decryptData(string $encrypted): string
    {
        try {
            return $this->crypto->data($encrypted)->decrypt();
        } catch (Exception $e) {
            throw new Exception("Failed to decrypt token", 400);
        }
    }

    public function encodeJson(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new Exception("Failed to encode token to JSON", 500);
        }

        return $json;
    }

    public function decodeJson(string $json): array
    {
        $payload = json_decode($json, true);

        if (!is_array($payload)) {
            throw new Exception("Invalid token payload", 400);
        }

        return $payload;
    }

}