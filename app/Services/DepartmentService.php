<?php

namespace App\Services;

use App\Models\Department;
use App\Repositories\DepartmentRepository;

class DepartmentService
{

    public function __construct(private DepartmentRepository $departmentRepository)
    {
    }

    public function create(string $tenant, array $data, bool $default = false): Department
    {
        return $this->departmentRepository->create($tenant, [
            'company_license_number' => $data['license_number'],
            'department_name' => $default ? config('settings.default_department_name') : $data['department_name'],
            'default' => $default
        ]);

    }

    public function getDefaultDepartment(string $tenant): ?Department
    {
        return $this->departmentRepository->getDefaultDepartment($tenant);
    }



}

