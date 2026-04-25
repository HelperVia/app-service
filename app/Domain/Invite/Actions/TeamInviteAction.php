<?php

namespace App\Domain\Invite\Actions;

use App\Domain\Invite\Actions\TeamInviteValidateAction;
use App\Actions\Utils\CreateShortLinkAction;
use App\Domain\Agent\DTO\CreateAgentData;
use App\Domain\Invite\DTO\CreateInviteData;
use App\Exceptions\ApiException;
use App\Models\Companies;
use App\Models\User;
use App\Domain\Agent\Services\AgentService;
use App\Domain\Department\Services\DepartmentService;
use App\Services\InviteService;
use App\Services\UserService;
use Throwable;
use DB;
use App\Domain\Agent\Constants\Agent;
class TeamInviteAction
{


    public function __construct(
        private readonly TeamInviteValidateAction $teamInviteValidate,
        private readonly InviteService $inviteService,
        private readonly UserService $userService,
        private readonly DepartmentService $departmentService,
        private readonly AgentService $agentService,
        private readonly CreateShortLinkAction $createShortLinkAction
    ) {
    }

    public function execute(array $data, User $user, Companies $company)
    {

        $this->validateInput($data);

        try {

            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();

            $email = $data['email'] ?? null;
            $role = $data['role'] ?? null;




            $this->checkEmailAvailability($email, $company);

            $invitedUser = $this->userService->getUserByEmail($email);
            $invite = $this->createInvite($user, $email, $role, $invitedUser, $company);
            $agent = $this->createAgent($user, $invite, $role, $company);
            $shortLink = $this->createShortLink($invite);

            if (!$agent?->id) {
                throw new ApiException('Agent could not be created', 500);
            }
            DB::connection('mongodb')->commit();
            DB::commit();



            return [
                'agent' => $agent,
                'invite_id' => $invite->id,
                'short_token' => $shortLink->short_token
            ];

        } catch (Throwable $e) {
            report($e);
            DB::connection('mongodb')->rollBack();
            DB::rollBack();
            throw $e;
        }

    }

    private function checkEmailAvailability(string $email, Companies $company): void
    {
        $validation = $this->teamInviteValidate->execute($email, $company);

        if ($validation['taken']) {

            throw new ApiException('The email address is already registered with your team', 409);
        }

    }

    private function validateInput(array $data): void
    {
        if (empty($data['email'])) {
            throw new ApiException('Email is required', 422);
        }

        if (empty($data['role'])) {
            throw new ApiException('Role is required', 422);
        }
    }

    private function createInvite(
        User $user,
        string $email,
        string $role,
        ?User $invitedUser,
        Companies $company
    ) {

        $temporaryName = $this->inviteService->generateTemporaryName($email, $invitedUser);

        $inviteData = new CreateInviteData(
            inviting_company_id: $company->id,
            invited_email: $email,
            invited_id: $invitedUser?->id,
            inviting_user: $user->id,
            invited_role: $role,
            temporary_name: $temporaryName
        );

        return $this->inviteService->create($inviteData);
    }
    private function createShortLink($invite)
    {
        return $this->createShortLinkAction->execute([
            'target' => $invite->invite_code,
            'type' => 'invite'
        ]);
    }
    private function createAgent($user, $invite, string $role, Companies $company)
    {


        $agentData = new CreateAgentData(
            license_number: $company->license_number,
            role: $role,
            user_id: $invite->invited_id,
            status: Agent::AGENT_STATUS_PENDING,
            agent_name: $invite->temporary_name
        );

        $agent = $this->agentService->updateOrInsertByUserID(
            $company->license_number,
            $agentData,
            $agentData->user_id
        );

        if (!$agent?->id) {
            throw new ApiException('Agent could not be created', 500);
        }

        return $agent;
    }
}