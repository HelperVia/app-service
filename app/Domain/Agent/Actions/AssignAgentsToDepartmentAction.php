<?php

namespace App\Domain\Agent\Actions;

use App\Domain\Agent\DTO\ModifyAgentDepartmentData;
use App\Domain\Agent\Services\AgentService;
use App\Exceptions\ApiException;
use Throwable;

class AssignAgentsToDepartmentAction
{

    public function __construct(private readonly AgentService $agentService)
    {
    }

    public function execute(array $agent_ids, string $tenant, $department_id): int
    {
        if (empty($agent_ids)) {
            return 0;
        }


        $updateData = new ModifyAgentDepartmentData(
            $department_id,
            false
        );

        return $this->agentService->addDepartmentToAgents($tenant, $updateData, $agent_ids);

    }
}