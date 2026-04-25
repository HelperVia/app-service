<?php

namespace App\Domain\Agent\DTO\Validatable;
use App\Domain\Agent\Constants\Agent;
use App\Constants\YesNo;
use App\Domain\Department\Services\DepartmentService;
use App\Rules\MongoObjectId;
use function PHPUnit\Framework\isArray;
class Validate
{



    protected function validateStatus($status)
    {
        $validStatuses = [
            Agent::AGENT_STATUS_ACTIVE,
            Agent::AGENT_STATUS_DELETED,
            Agent::AGENT_STATUS_PENDING,
            Agent::AGENT_STATUS_SUSPENDED,
            Agent::AGENT_STATUS_CANCELED
        ];

        if (!in_array($status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                "Invalid agent status '{$status}'. Valid statuses: " . implode(", ", $validStatuses)
            );
        }
    }

    protected function validateDepartmentIds(array|string &$department_ids, $getDefault = true)
    {
        $rule = new MongoObjectId();

        // Normalize input: always work with an array
// This allows the same validation logic for a single ID or multiple IDs
        $ids = is_array($department_ids) ? $department_ids : [$department_ids];



        // Validate MongoDB ObjectId format
// Ensures each provided department ID is a valid ObjectId
// Throws an exception immediately if any ID is invalid

        if ($ids) {
            foreach ($ids as $id) {
                $rule->validate('department_id', $id, function ($message) {
                    throw new \InvalidArgumentException($message);
                });
            }
        }


        // Retrieve tenant identifier from the authenticated user
// All department validations must be tenant-scoped
        $tenant = auth()->user()->license;

        // Fetch all available departments for this tenant
// The service layer handles status filtering and default department logic internally
        $departmentService = app(DepartmentService::class);
        $availableDepartments = $departmentService->availableDepartmentById(
            $tenant,
            $ids,
            $getDefault
        );

        // Extract only department IDs from the validated department records
// This guarantees that only allowed and existing department IDs are used
        $department_ids = array_map(function ($dept) {
            return $dept['id'];
        }, $availableDepartments);

        $department_ids = array_unique($department_ids);



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
            Agent::AGENT_AUTO_ASSIGN_DISABLE,
            Agent::AGENT_AUTO_ASSIGN_ENABLE
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