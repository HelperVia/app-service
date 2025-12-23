<?php

namespace App\Services;

use App\DTO\Agent\ChangeAgentStatusData;
use App\DTO\Agent\CreateAgentData;
use App\DTO\Agent\UpdateAgentData;
use App\Models\Agent;
use App\Repositories\AgentRepository;

class AgentService
{



    public function __construct(private AgentRepository $agentRepository)
    {
    }

    private function createData(CreateAgentData $data)
    {
        return [
            'socket' => $data->license_number . '_' . $data->user_id . '_operators',
            'license_number' => (string) $data->license_number,
            'user_id' => $data->user_id,
            'status' => $data->status,
            'away' => $data->away,
            'active_chat' => $data->active_chat,
            'chat_limit' => $data->chat_limit,
            'department' => $data->department,
            'auto_assign' => $data->auto_assign,
            'role' => $data->role,
        ];
    }

    public function create(string $tenant, CreateAgentData $data): Agent
    {
        return $this->agentRepository->create($tenant, $this->createData($data));
    }
    public function getAgents(string $tenant)
    {
        return $this->agentRepository->getAgents($tenant);
    }

    public function update(string $tenant, array $data, array $where): ?Agent
    {
        return $this->agentRepository->update($tenant, $data, $where);
    }
    public function updateOrInsertByUserID(string $tenant, CreateAgentData $data, string $user_id)
    {
        return $this->agentRepository->updateOrInsertByUserID($tenant, $this->createData($data), $user_id);
    }

    public function updateAgentByUserID(string $tenant, string $user_id, UpdateAgentData $data)
    {
        return $this->update($tenant, $data->toArray(), [
            'user_id' => $user_id
        ]);
    }


}