<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Domain\Agent\Constants\Agent as AgentConstants;
use Illuminate\Database\Eloquent\Collection;
class AgentRepository
{


    public function __construct(private Agent $model)
    {
    }

    public function create(string $tenant, array $data): Agent
    {
        return $this->model->tenant($tenant)->create($data);
    }
    public function getAgents(string $tenant)
    {
        return $this->model->tenant($tenant)->whereNotIn('status', [
            AgentConstants::AGENT_STATUS_DELETED,
            AgentConstants::AGENT_STATUS_CANCELED
        ])->get();
    }

    public function getAll(string $tenant, array $where): Collection
    {
        return $this->model->tenant($tenant)->where($where)->get();
    }

    public function get(string $tenant, array $where): ?Agent
    {
        return $this->model->tenant($tenant)->where($where)->first();
    }
    public function toggleSuspend(string $tenant, string $id): ?Agent
    {
        $agent = $this->model
            ->tenant($tenant)
            ->where('_id', $id)
            ->whereIn('status', [
                AgentConstants::AGENT_STATUS_ACTIVE,
                AgentConstants::AGENT_STATUS_SUSPENDED,
            ])
            ->first();

        if (!$agent) {
            return null;
        }

        $agent->status = $agent->status === AgentConstants::AGENT_STATUS_ACTIVE
            ? AgentConstants::AGENT_STATUS_SUSPENDED
            : AgentConstants::AGENT_STATUS_ACTIVE;

        $agent->save();

        return $agent;
    }
    public function update(string $tenant, array $data, array $where): ?Agent
    {
        $agent = $this->model->tenant($tenant)->where($where)->first();

        if (!$agent) {
            return null;
        }
        $agent->fill($data);
        if ($agent->isDirty()) {
            $agent->save();
            return $agent->fresh();
        }
        return $agent;
    }

    public function detachDepartmentFromAgents(string $tenant, string $department_id, array $agent_ids): int
    {

        $query = $this->model->tenant($tenant)
            ->where('department_ids', $department_id);

        if (!empty($agent_ids)) {
            $query->whereIn('_id', $agent_ids);
        }


        return $query->pull('department_ids', $department_id);


    }

    public function addDepartmentToAgents(string $tenant, array|string $department_id, array $agent_ids): int
    {

        $department_ids = is_array($department_id) ? $department_id : [$department_id];

        $result = $this->model->tenant($tenant)
            ->whereIn('_id', $agent_ids)
            ->update([
                '$addToSet' => [
                    'department_ids' => ['$each' => $department_ids]
                ]
            ]);


        return $result;
    }


    public function updateOrInsertByUserID(string $tenant, array $data, string $user_id): Agent
    {
        return $this->model->tenant($tenant)->updateOrCreate(['user_id' => $user_id], $data);
    }
    public function isValidAgentByUserID(string $tenant, string $user_id): ?string
    {
        $id = $this->model
            ->tenant($tenant)
            ->where([
                'user_id' => $user_id,
                'status' => AgentConstants::AGENT_STATUS_ACTIVE
            ])
            ->value('_id');
        return $id ? (string) $id : null;
    }



}