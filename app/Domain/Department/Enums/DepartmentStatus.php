<?php

namespace App\Domain\Department\Enums;


enum DepartmentStatus: string
{

    case ACTIVE = 'A';
    case INACTIVE = 'S';
    case DELETED = 'D';


    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DELETED => 'Deleted',
        };
    }


    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }


}