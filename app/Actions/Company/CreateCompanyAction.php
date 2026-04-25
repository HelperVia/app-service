<?php

namespace App\Actions\Company;

use App\Domain\Agent\DTO\CreateAgentData;
use App\Domain\Agent\Services\AgentService;
use App\Domain\Settings\DTO\UpdateSettingsData;
use App\Domain\Settings\Services\SettingService;
use App\Exceptions\ApiException;
use App\Services\CompanyService;
use App\Constants\Companies;
use App\Domain\Agent\Constants\Agent;
use App\Domain\Department\Services\DepartmentService;
use App\Services\UserService;
use App\Models\Department;
use Throwable;

class CreateCompanyAction
{


    public function __construct(
        readonly SettingService $settingService,
        private CompanyService $companyService,
        private UserService $userService,
        private AgentService $agentService,
        private DepartmentService $departmentService
    ) {
    }

    public function execute($user, $invite = null): array
    {

        $licenseNumber = $this->companyService->resolveLicenseNumber($invite);

        $company = $invite ? $invite['company']
            : $this->companyService->create([
                'name' => 'Unnamed Company',
                'license_number' => $licenseNumber,
                'step' => 0,
                'status' => Companies::COMPANY_STATUS_ACTIVE
            ]);


        $this->userService->attachCompany($user, $company->id, ['role' => $invite ? $invite->invited_role : Agent::AGENT_ROLE_OWNER]);

        if (empty($invite)) {
            $createdDepartment = $this->createDepartment($licenseNumber);

            $department[] = $createdDepartment['id'];
            $agentData = new CreateAgentData(
                license_number: $licenseNumber,
                user_id: $user->id,
                department_ids: $department,
                role: Agent::AGENT_ROLE_OWNER
            );
            $this->agentService->create($licenseNumber, $agentData);

            $settingsData = new UpdateSettingsData(
                prechatform: array_merge(config('settings.prechatform.en'), ['enabled' => config('settings.prechatform_enabled')]),
                postchatform: array_merge(config('settings.postchatform.en'), ['enabled' => config('settings.postchatform_enabled')]),
                widget_customization: config('settings.widget.customization'),
                widget_language: ['language' => 'en', 'translations' => config('settings.widget_default_languages.en.translations')]
            );
            $this->settingService->create($licenseNumber, $settingsData);



        }


        return [$company, $licenseNumber];
    }

    public function createDepartment(string $tenant): ?array
    {

        try {
            return $this->departmentService->createDefaultDepartment($tenant);
        } catch (Throwable $e) {
            throw new ApiException($e->getMessage(), 400);
        }


    }


}