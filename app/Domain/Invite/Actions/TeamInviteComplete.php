<?php

namespace App\Domain\Invite\Actions;

use App\Domain\Agent\Constants\Agent;
use App\Domain\Agent\DTO\UpdateAgentData;
use App\Domain\Invite\DTO\ChangeInviteStatusData;
use App\Exceptions\ApiException;
use App\Models\Invite;
use App\Domain\Invite\Constants\InviteStatus;
use App\Models\User;
use App\Domain\Agent\Services\AgentService;
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
        $this->deleteShortLink($invite);


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

    private function deleteShortLink(Invite $invite)
    {
        $deleted = $this->shortLinkService->deleteByShortToken($invite->shortLink->short_token);

        if (!$deleted) {
            throw new ApiException('The invite short link could not be deleted.');
        }
    }



    private function changeInviteStatus(Invite $invite): void
    {
        $inviteStatusData = new ChangeInviteStatusData(
            status: InviteStatus::INVITE_COMPLETE
        );

        $this->inviteService->changeStatus($invite, $inviteStatusData);
    }

    private function updateAgent(Invite $invite, User $user)
    {
        $agentData = new UpdateAgentData(
            status: Agent::AGENT_STATUS_ACTIVE,
            user_id: $user->id
        );

        $agent = $this->agentService->updateAgentByUserID(
            $invite->company->license_number,
            $invite->invited_id,
            $agentData,
        );

        if (!$agent) {
            throw new ApiException('The agent status could not be updated.');
        }
    }

}