<?php

namespace App\Repositories;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Models\Agent;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class DepartmentRepository implements DepartmentRepositoryInterface
{

    public function __construct(private Department $model)
    {
    }


    public function getDefaultDepartment(string $tenant): array
    {
        $model = $this->model->tenant($tenant)->where('default', 'Y')->first();

        return $model ? $model->toArray() : [];
    }

    public function createOrUpdateByKey(array $data, string $key, string $tenant = ''): array
    {
        $query = $this->model->tenant($tenant)->updateOrCreate(
            ['department_key' => $key],
            $data
        );

        return $query->toArray();
    }

    public function getDepartments(string $tenant, array $where = [], array $whereIn = []): Collection
    {
        $query = $this->model->tenant($tenant);

        foreach ($where as $condition) {
            if (is_array($condition) && count($condition) === 3) {

                [$column, $operator, $value] = $condition;
                $query->where($column, $operator, $value);

            } elseif (is_array($condition) && count($condition) === 2) {

                [$column, $value] = $condition;
                $query->where($column, $value);
            }
        }

        foreach ($whereIn as $column => $values) {
            $query->whereIn($column, $values);
        }

        return $query->get();
    }


    public function update(array $data, array $where, string $tenant): bool
    {
        return $this->model->tenant($tenant)->where($where)->update($data) > 0;
    }
    public function getTeamDepartment(string $tenant, string $status, array $department_ids = []): array
    {


        $query = $this->model->tenant($tenant);

        if (!empty($department_ids)) {
            $query = $query->whereIn('id', $department_ids);
        }

        return $query->whereNot('status', $status)->get()->all();

    }
    public function getWithAgents(string $tenant, string $status, array $department_ids = []): array
    {
        $departments = collect($this->getTeamDepartment($tenant, $status, $department_ids));


        $agents = Agent::all();

        $departmentsWithAgents = $departments->map(function ($department) use ($agents) {


            $departmentAgents = $agents->filter(
                fn($agent) => in_array($department->id, $agent->department_ids ?? [])
            );
            $department->agents = $departmentAgents->values()->all();

            return $department;
        });

        return $departmentsWithAgents->all();
    }

    public function availableDepartmentById(
        string $tenant,
        string $status,
        array|string $ids,
        string $defaultStatus,
        bool $get_default = true
    ): array {

        $query = $this->model
            ->tenant($tenant)
            ->where('status', '<>', $status)
            ->where(function ($q) use ($ids, $defaultStatus, $get_default) {
                if ($get_default) {
                    $q->where('default', $defaultStatus)
                        ->orWhereIn('_id', is_array($ids) ? $ids : [$ids]);
                } else {
                    $q->whereIn('_id', is_array($ids) ? $ids : [$ids]);
                }
            });


        return $query->get()->toArray();
    }

    public function findById(string $tenant, string $id): array
    {
        $model = $this->model->tenant($tenant)->find($id);
        return $model ? $model->toArray() : [];
    }
    public function updateById(string $tenant, string $id, array $data): bool
    {
        $affected = $this->model->tenant($tenant)
            ->where('_id', $id)
            ->update($data);

        if ($affected === 0) {
            throw new \Exception('Update failed or record not found');
        }

        return true;
    }

}