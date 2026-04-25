<?php

namespace App\Domain\Agent\Actions;

use App\Domain\Agent\Constants\Agent;
use App\Domain\Agent\Services\AgentService;
use App\Exceptions\ApiException;

class SuspendAgentAction
{


    public function __construct(readonly private AgentService $agentService)
    {
    }

    public function execute(string $license, string $id): \App\Models\Agent
    {
        $agent = $this->toggleSuspend($license, $id);

        return $agent;

    }

    private function toggleSuspend(string $license, string $id): ?\App\Models\Agent
    {
        $agent = $this->agentService->toggleSuspend($license, $id);

        if (!$agent) {
            throw new ApiException('Agent not found or not suspendable', 422);
        }


        return $agent;

    }
}