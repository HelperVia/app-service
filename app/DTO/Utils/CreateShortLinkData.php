<?php

namespace App\DTO\Utils;
use Illuminate\Support\Str;
class CreateShortLinkData
{

    public function __construct(
        public readonly string $target,
        public readonly string $type,
        public ?string $short_token = null,


    ) {
        $this->short_token ??= Str::random(10);

    }

}
