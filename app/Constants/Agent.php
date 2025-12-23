<?php
namespace App\Constants;

class Agent
{

    const AGENT_ROLE_OWNER = 'O';
    const AGENT_ROLE_SUPERADMIN = 'S';
    const AGENT_ROLE_AGENT = 'A';
    const AGENT_ROLE_UNKNOW = '?';

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


    public const ROLE_LABELS = [
        self::AGENT_ROLE_OWNER => 'Owner',
        self::AGENT_ROLE_SUPERADMIN => 'Super Admin',
        self::AGENT_ROLE_UNKNOW => 'Unknow',
        self::AGENT_ROLE_AGENT => 'Agent'
    ];

    public static function getRoleLabel($role)
    {
        return self::ROLE_LABELS[$role] ?? "Unknow";
    }

}