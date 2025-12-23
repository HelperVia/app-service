<?php

namespace App\Actions\Company;

use App\DTO\Agent\CreateAgentData;
use App\Services\AgentService;
use App\Services\CompanyService;
use App\Constants\Companies;
use App\Constants\Agent;
use App\Services\DepartmentService;
use App\Services\UserService;
use App\Constants\YesNo;
class CreateCompanyAction
{


    public function __construct(
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
            $departmentID = $this->departmentService->create(
                $licenseNumber,
                [
                    'license_number' => $licenseNumber,
                ],
                true
            )->id;

            $department[$departmentID] = 1;
            $agentData = new CreateAgentData(
                license_number: $licenseNumber,
                user_id: $user->id,
                department: $department,
                role: Agent::AGENT_ROLE_OWNER
            );
            $this->agentService->create($licenseNumber, $agentData);
        }


        return [$company, $licenseNumber];
    }


}