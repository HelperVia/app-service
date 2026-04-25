<?php

namespace App\Domain\Invite\Actions;

use App\Models\Companies;
use App\Services\CompanyService;
use App\Services\InviteService;

class TeamInviteValidateAction
{

    public function __construct(
        private CompanyService $companyService,
        private InviteService $inviteService
    ) {

    }
    public function execute(string $email, Companies $company): array
    {


        $user = $this->companyService->getUserByEmail($company, $email);

        if ($user) {
            return [
                'user_id' => $user->id,
                'email' => $email,
                'taken' => true,
            ];
        }


        $invite = $this->inviteService->getActiveInviteByCompanyAndEmail($company->id, $email);

        if ($invite) {
            return [
                'user_id' => $invite->invited_id,
                'email' => $email,
                'taken' => true,
            ];
        }


        return [
            'user_id' => 0,
            'email' => $email,
            'taken' => false,
        ];
    }
}