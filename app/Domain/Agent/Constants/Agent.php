<?php
namespace App\Domain\Agent\Constants;

class Agent
{
    /**
     * AGENT ROLE STATUS
     */

    const AGENT_ROLE_OWNER = 'O';
    const AGENT_ROLE_SUPERADMIN = 'S';
    const AGENT_ROLE_AGENT = 'A';
    const AGENT_ROLE_UNKNOW = '?';


    /**
     * AGENT AUTO ASSIGN
     */

    const AGENT_AUTO_ASSIGN_ENABLE = "Y";
    const AGENT_AUTO_ASSIGN_DISABLE = "N";



    /**
     * AGENT AWAY STATUS
     */

    const AGENT_AWAY_ENABLE = 'Y';
    const AGENT_AWAY_DISABLE = 'N';


    /**
     *  AGENT STATUS
     */

    const AGENT_STATUS_PENDING = 'P';
    const AGENT_STATUS_ACTIVE = 'A';
    const AGENT_STATUS_DELETED = 'D';
    const AGENT_STATUS_SUSPENDED = 'S';
    const AGENT_STATUS_CANCELED = 'X';

    public const ROLE_LABELS = [
        self::AGENT_ROLE_OWNER => 'Owner',
        self::AGENT_ROLE_SUPERADMIN => 'Super Admin',
        self::AGENT_ROLE_UNKNOW => 'Unknow',
        self::AGENT_ROLE_AGENT => 'Agent'
    ];

    public static function getRoleLabel($role)
    {
        return self::ROLE_LABELS[$role] ?? "Unknown";
    }


    public const STATUS_LABELS = [
        self::AGENT_STATUS_PENDING => 'Pending',
        self::AGENT_STATUS_ACTIVE => 'Active',
        self::AGENT_STATUS_DELETED => 'Deleted',
        self::AGENT_STATUS_SUSPENDED => 'Suspended',
        self::AGENT_STATUS_CANCELED => 'Canceled'
    ];

    public static function getStatusLabel($status)
    {
        return self::STATUS_LABELS[$status] ?? "Unknown";
    }

}