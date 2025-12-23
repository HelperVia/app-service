<?php

namespace App\Utils;

class Crypto
{
    private $key = '12385296547';
    private $data = null;

    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    public function key($key)
    {
        $this->key = $key;
        return $this;
    }

    public function encrypt()
    {
        $result = '';
        $keyLength = strlen($this->key);

        for ($i = 0; $i < strlen($this->data); $i++) {
            $char = $this->data[$i];
            $keychar = $this->key[$i % $keyLength];
            $result .= chr(ord($char) + ord($keychar));
        }

        return base64_encode($result);
    }

    public function decrypt()
    {
        $result = '';
        $decoded = base64_decode($this->data);
        $keyLength = strlen($this->key);

        for ($i = 0; $i < strlen($decoded); $i++) {
            $char = $decoded[$i];
            $keychar = $this->key[$i % $keyLength];
            $result .= chr(ord($char) - ord($keychar));
        }

        return $result;
    }
}