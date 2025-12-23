<?php

namespace App\Actions\Team\Invite;

use App\Actions\Utils\CreateShortLinkAction;
use App\DTO\Agent\CreateAgentData;
use App\DTO\Invite\CreateInviteData;
use App\Exceptions\ApiException;
use App\Services\AgentService;
use App\Services\DepartmentService;
use App\Services\InviteService;
use App\Services\UserService;
use Throwable;
use DB;
use App\Constants\Agent;
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

    public function execute(array $data)
    {

        $this->validateInput($data);

        try {

            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();

            $email = $data['email'] ?? null;
            $role = $data['role'] ?? null;
            $user = auth()->user();


            $this->checkEmailAvailability($email);

            $invitedUser = $this->userService->getUserByEmail($email);
            $invite = $this->createInvite($user, $email, $role, $invitedUser);
            $agent = $this->createAgent($user, $invite, $role);
            $shortLink = $this->createShortLink($invite);

            if (!$agent?->id) {
                throw new ApiException('Agent could not be created', 500);
            }
            DB::connection('mongodb')->commit();
            DB::commit();



            return [
                'invite_id' => $invite->id
            ];

        } catch (Throwable $e) {
            DB::connection('mongodb')->rollBack();
            DB::rollBack();
            throw $e;
        }

    }

    private function checkEmailAvailability(string $email): void
    {
        $validation = $this->teamInviteValidate->execute($email);

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

    private function createInvite($user, string $email, string $role, $invitedUser)
    {
        $temporaryName = $this->inviteService->generateTemporaryName($email, $invitedUser);

        $inviteData = new CreateInviteData(
            inviting_company_id: $user->company->id,
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
    private function createAgent($user, $invite, string $role)
    {


        $agentData = new CreateAgentData(
            license_number: $user->license,
            role: $role,
            user_id: $invite->invited_id,
            status: Agent::AGENT_STATUS_PENDING
        );

        $agent = $this->agentService->updateOrInsertByUserID(
            $user->license,
            $agentData,
            $agentData->user_id
        );

        if (!$agent?->id) {
            throw new ApiException('Agent could not be created', 500);
        }

        return $agent;
    }
}