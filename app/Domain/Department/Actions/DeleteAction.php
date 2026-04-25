<?php

namespace App\Domain\Department\Actions;

use App\Domain\Agent\Services\AgentService;
use App\Domain\Department\Services\DepartmentService;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteAction
{
    public function __construct(
        private readonly DepartmentService $departmentService,
        private readonly AgentService $agentService
    ) {
    }

    public function execute(string $id, string $tenant): string
    {
        return DB::connection('mongodb')->transaction(function () use ($id, $tenant) {
           
        try {

                $this->deleteDepartment($id, $tenant);
                $this->detachDepartmentFromAgents($tenant, $id);

                return $id;

            } catch (Throwable $e) {
                Log::error('Failed to delete department', [
                    'tenant' => $tenant,
                    'department_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                throw $e;
            }
        });
    }

    private function deleteDepartment(string $id, string $tenant): void
    {
        $deleted = $this->departmentService->deleteDepartmentById($id, $tenant);

        if (!$deleted) {
            throw new ApiException('Failed to delete department');
        }
    }

    private function detachDepartmentFromAgents(string $tenant, string $departmentId): void
    {
        $this->agentService->detachDepartmentFromAgents(
            $tenant,
            $departmentId,
            ['department_ids' => $departmentId]
        );
    }
}