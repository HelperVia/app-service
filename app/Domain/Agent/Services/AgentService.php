<?php

namespace App\Domain\Agent\Services;


use App\Domain\Agent\DTO\CreateAgentData;
use App\Domain\Agent\DTO\ModifyAgentDepartmentData;
use App\Domain\Agent\DTO\UpdateAgentData;
use App\Models\Agent;
use App\Repositories\AgentRepository;
use App\Domain\Agent\Constants\Agent as AgentStatus;
use App\Rules\MongoObjectId;
use Illuminate\Database\Eloquent\Collection;

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
            'department_ids' => $data->department_ids,
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

    public function update(string $tenant, UpdateAgentData $data, array $where): ?Agent
    {

        return $this->agentRepository->update($tenant, $data->toArray(), $where);
    }
    public function updateOrInsertByUserID(string $tenant, CreateAgentData $data, string $user_id)
    {
        return $this->agentRepository->updateOrInsertByUserID($tenant, $this->createData($data), $user_id);
    }

    public function updateAgentByUserID(string $tenant, string $user_id, UpdateAgentData $data)
    {
        return $this->update($tenant, $data, [
            'user_id' => $user_id
        ]);
    }
    public function getActiveAgentByID(string $tenant, string $id): ?Agent
    {
        return $this->get($tenant, [
            '_id' => $id,
            'status' => AgentStatus::AGENT_STATUS_ACTIVE
        ]);
    }


    public function isValidAgentByUserID(string $tenant, string $user_id): ?string
    {
        return $this->agentRepository->isValidAgentByUserID($tenant, $user_id);
    }

    public function get(string $tenant, array $where = []): ?Agent
    {
        return $this->agentRepository->get($tenant, $where);
    }
    public function getAll(string $tenant, array $where = []): Collection
    {
        return $this->agentRepository->getAll($tenant, $where);
    }



    public function toggleSuspend(string $tenant, string $id): ?Agent
    {
        return $this->agentRepository->toggleSuspend($tenant, $id);

    }
    public function getAgentByDepartmentId(string $tenant, string $department_id): Collection
    {
        return $this->getAll($tenant, [
            'department_ids' => $department_id
        ]);
    }

    public function addDepartmentToAgents(string $tenant, ModifyAgentDepartmentData $data, array $agent_id): int
    {
        return $this->agentRepository->addDepartmentToAgents($tenant, $data->department_id, $agent_id);
    }

    public function detachDepartmentFromAgents(string $tenant, string $department_id, array $agent_ids = [])
    {

        return $this->agentRepository->detachDepartmentFromAgents($tenant, $department_id, $agent_ids);

    }
}