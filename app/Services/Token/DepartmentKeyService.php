<?php

namespace App\Services\Token;

use App\Contracts\Token\TokenInterface;
use InvalidArgumentException;
use Normalizer;


class DepartmentKeyService implements TokenInterface
{


    public function encode(array $data): string
    {
        //  Unicode normalize the string (important for consistent representation)
        $name = normalizer_normalize($data['department_name'], Normalizer::FORM_C);

        //  Remove invisible/control characters and extra spaces
        $name = preg_replace('/[\p{Z}\p{C}]+/u', ' ', $name);
        $name = trim($name);

        //  Convert to lowercase (Unicode safe)
        $name = mb_strtolower($name);

        //  Make it URL-safe (slug)
        // Replace spaces and non-alphanumeric characters with a hyphen
        $name = preg_replace('/[^\p{L}\p{N}]+/u', '-', $name);
        $name = preg_replace('/-+/', '-', $name); // replace multiple hyphens with single one
        $name = trim($name, '-'); // remove hyphens from start/end
        $name .= "-" . time();
        return $name;
    }


    public function decode(string $token): array
    {
        return ['department_name' => str_replace('-', ' ', $token)];
    }

    public function validateEncodeData(array $data): void
    {
        if (!isset($data['department_name'])) {
            throw new InvalidArgumentException("Missing required department_name fields");
        }
    }
}

