<?php

namespace App\Domain\Invite\Actions;

use App\Domain\Agent\Constants\Agent;
use App\Exceptions\ApiException;
use App\Domain\Agent\Services\AgentService;

class CancelInviteByAgent
{


    public function __construct(
        readonly private AgentService $agentService,
        readonly private CancelInviteAction $cancelInviteAction
    ) {
    }
    public function execute(string $id, string $license)
    {
        $agent = $this->getAgent($id, $license);

        return $this->cancelInviteAction->execute($agent->user_id, $license);

    }

    private function getAgent(string $id, string $license): \App\Models\Agent
    {
        $agent = $this->agentService->get($license, [
            '_id' => $id,
            'status' => Agent::AGENT_STATUS_PENDING
        ]);

        if (!$agent) {
            throw new ApiException('The agent was not found or is no longer in a pending state.', 422);
        }
        return $agent;
    }
}