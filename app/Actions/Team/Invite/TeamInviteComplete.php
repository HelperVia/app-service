<?php

namespace App\Actions\Team\Invite;

use App\Constants\Agent;
use App\DTO\Agent\UpdateAgentData;
use App\DTO\Invite\ChangeInviteStatusData;
use App\Exceptions\ApiException;
use App\Models\Invite;
use App\Models\User;
use App\Services\AgentService;
use App\Services\InviteService;
use App\Services\ShortLinkService;

class TeamInviteComplete
{
    public function __construct(
        private readonly InviteService $inviteService,
        private readonly ShortLinkService $shortLinkService,
        private readonly AgentService $agentService
    ) {
    }

    public function execute(Invite $invite, User $user): void
    {
        $this->validateInvite($invite);
        $this->changeInviteStatus($invite);
        $this->updateAgent($invite, $user);

        if ($invite->shortLink) {
            $this->shortLinkService->deleteByShortToken($invite->shortLink->short_token);
        }

    }

    private function validateInvite(Invite $invite): void
    {
        if (!$invite) {
            throw new ApiException('Invite not found');
        }

        if (!$invite->company) {
            throw new ApiException('Invite company not found');
        }

        if (!$invite->invited_id) {
            throw new ApiException('Invited user ID not found');
        }
    }



    private function changeInviteStatus(Invite $invite): void
    {
        $inviteStatusData = new ChangeInviteStatusData(
            status: \App\Constants\Invite::INVITE_COMPLETE
        );

        $this->inviteService->changeStatus($invite, $inviteStatusData);
    }

    private function updateAgent(Invite $invite, User $user)
    {
        $agentData = new UpdateAgentData(
            status: Agent::AGENT_STATUS_ACTIVE,
            user_id: $user->id
        );

        $this->agentService->updateAgentByUserID(
            $invite->company->license_number,
            $invite->invited_id,
            $agentData,
        );
    }

}