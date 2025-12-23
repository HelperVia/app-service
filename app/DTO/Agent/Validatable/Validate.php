<?php

namespace App\DTO\Agent\Validatable;
use App\Constants\Agent;
use App\Constants\YesNo;
class Validate
{

    protected function validateStatus($status)
    {
        $validStatuses = [
            Agent::AGENT_STATUS_ACTIVE,
            Agent::AGENT_STATUS_DELETED,
            Agent::AGENT_STATUS_PENDING,
            Agent::AGENT_STATUS_SUSPENDED
        ];

        if (!in_array($status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                "Invalid agent status '{$status}'. Valid statuses: " . implode(", ", $validStatuses)
            );
        }
    }

    protected function validateRoles($role)
    {
        $validRoles = [
            Agent::AGENT_ROLE_AGENT,
            Agent::AGENT_ROLE_OWNER,
            Agent::AGENT_ROLE_SUPERADMIN,
            Agent::AGENT_ROLE_UNKNOW
        ];

        if (!in_array($role, $validRoles, true)) {

            throw new \InvalidArgumentException(
                "Invalid agent role '{$role}'. Valid roles: " . implode(", ", $validRoles)
            );
        }
    }

    protected function validateAway($away)
    {
        $validAway = [
            Agent::AGENT_AWAY_ENABLE,
            Agent::AGENT_AWAY_DISABLE,
        ];

        if (!in_array($away, $validAway, true)) {

            throw new \InvalidArgumentException(
                "Invalid agent away '{$away}'. Valid aways: " . implode(", ", $validAway)
            );
        }
    }

    protected function validateAutoAssign($auto_assign)
    {
        $validAutoAssign = [
            YesNo::NO,
            YesNo::YES
        ];

        if (!in_array($auto_assign, $validAutoAssign, true)) {

            throw new \InvalidArgumentException(
                "Invalid agent auto_assign '{$auto_assign}'. Valid auto_assigns: " . implode(", ", $validAutoAssign)
            );
        }
    }

    protected function validateChatLimit($chat_limit)
    {
        if ($chat_limit < 0) {
            throw new \InvalidArgumentException("chat_limit cannot be negative");
        }
    }

    protected function validateActiveChat($active_chat)
    {
        if ($active_chat < 0) {
            throw new \InvalidArgumentException("active_chat cannot be negative");
        }
    }

}