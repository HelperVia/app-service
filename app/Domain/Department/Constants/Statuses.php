<?php
namespace App\Domain\Department\Constants;

class Statuses
{
    /**
     * DEPARTMENT STATUS
     */

    const DEPARTMENT_STATUS_ACTIVE = 'A';
    const DEPARTMENT_STATUS_DELETED = 'D';

    const DEPARTMENT_STATUS_INACTIVE = 'S';



    public const DEPARTMENT_STATUS_LABELS = [
        self::DEPARTMENT_STATUS_ACTIVE => 'Active',
        self::DEPARTMENT_STATUS_DELETED => 'Deleted',
        self::DEPARTMENT_STATUS_INACTIVE => 'Inactive',
    ];

    public static function getStatusLabel($status)
    {
        return self::DEPARTMENT_STATUS_LABELS[$status] ?? "Unknown";
    }

    public static function getStatuses(): array
    {
        return self::DEPARTMENT_STATUS_LABELS;
    }






}