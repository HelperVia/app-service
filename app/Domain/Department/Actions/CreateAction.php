<?php

namespace App\Domain\Department\Actions;

use App\Domain\Agent\Actions\AssignAgentsToDepartmentAction;
use App\Domain\Agent\Services\AgentService;
use App\Domain\Department\DTO\CreateDepartmentData;
use App\Domain\Department\Services\DepartmentService;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateAction
{
    public function __construct(
        private readonly DepartmentService $departmentService,
        private readonly AgentService $agentService,
        private readonly AssignAgentsToDepartmentAction $assignAgentService
    ) {
    }

    public function execute(array $data, string $tenant): array
    {
        try {
            DB::connection('mongodb')->beginTransaction();


            $department = $this->createDepartment($data, $tenant);
            $this->assignAgentsToDepartment($data, $tenant, $department['id']);

            DB::connection('mongodb')->commit();

            return $department;

        } catch (Throwable $e) {
            DB::connection('mongodb')->rollBack();

            Log::error('Failed to create department', [
                'tenant' => $tenant,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    private function createDepartment(array $data, string $tenant): array
    {
        $dto = new CreateDepartmentData(
            department_name: $data['department_name']
        );

        return $this->departmentService->createOrUpdateByKey($dto, $tenant);
    }

    private function assignAgentsToDepartment(array $data, string $tenant, string $department_id): void
    {


        if (!empty($data['agent_ids'])) {
            $result = $this->assignAgentService->execute($data['agent_ids'], $tenant, $department_id);
            if ($result < 1) {
                throw new ApiException('No agents could be assigned to the department.');
            }
        }

    }
}