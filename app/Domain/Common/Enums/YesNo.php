<?php

namespace App\Domain\Common\Enums;

enum YesNo: string
{

    case YES = 'Y';
    case NO = 'N';

    public function label(): string
    {

        return match ($this) {
            self::YES => 'Yes',
            self::NO => 'No'
        };
    }

    public function toBool(): bool
    {
        return $this === self::YES;
    }

    public static function fromBool(bool $value): self
    {
        return $value ? self::YES : self::NO;
    }

}