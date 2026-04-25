<?php

namespace App\Domain\Department\DTO;



use App\Domain\Department\Enums\DepartmentDefaultFlag;
use App\Domain\Department\Enums\DepartmentStatus;
use App\Services\Token\DepartmentKeyService;
use App\Traits\DtoToArray;

class CreateDepartmentData
{

    use DtoToArray;

    public function __construct(
        public string $department_name,
        public readonly DepartmentDefaultFlag $default = DepartmentDefaultFlag::NOT_DEFAULT,
        public readonly DepartmentStatus $status = DepartmentStatus::ACTIVE,
        public ?string $department_key = null,
    ) {
        $this->department_name = mb_convert_case($this->department_name, MB_CASE_TITLE, 'UTF-8');
        $this->department_key ??= app(DepartmentKeyService::class)->encode(['department_name' => $this->department_name]);

    }
}