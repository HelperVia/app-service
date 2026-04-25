<?php

namespace App\Domain\Invite\Actions;

use App\Domain\Agent\Constants\Agent;
use App\Domain\Invite\Constants\InviteStatus;
use App\Domain\Agent\DTO\UpdateAgentData;
use App\Domain\Invite\DTO\UpdateInviteData;
use App\Exceptions\ApiException;
use App\Domain\Agent\Services\AgentService;
use App\Services\InviteService;
use App\Services\ShortLinkService;
use Throwable;
use DB;
class CancelInviteAction
{


    public function __construct(
        readonly private InviteService $inviteService,
        readonly private ShortLinkService $shortLinkService,
        readonly private AgentService $agentService
    ) {

    }
    public function execute(string $user_id, string $license): \App\Models\Invite
    {
        try {

            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();

            $invite = $this->getInvite($user_id, $license);
            $this->updateInvite($invite);
            $this->deleteShortLink($invite);
            $this->updateAgent($invite, $license);


            DB::connection('mongodb')->commit();
            DB::commit();



            return $invite;


        } catch (Throwable $e) {
            report($e);
            DB::connection('mongodb')->rollBack();
            DB::rollBack();

            throw $e;
        }
    }

    private function getInvite(string $user_id, string $license): \App\Models\Invite
    {
        $Invite = $this->inviteService->getInviteWithCompany(
            ['status' => InviteStatus::INVITE_PENDING, 'invited_id' => $user_id],
            ['license_number' => $license]
        );

        if (!$Invite) {
            throw new ApiException('Pending invitation not found for the given license.', 422);
        }
        return $Invite;
    }

    private function updateInvite(\App\Models\Invite $invite): bool
    {

        $updateData = new UpdateInviteData(
            status: InviteStatus::INVITE_CANCELED
        );
        $updated = $this->inviteService->save($invite, $updateData);
        if (!$updated) {
            throw new ApiException('The invitation could not be updated. It may already have the desired status.', 422);
        }


        return true;
    }

    private function deleteShortLink(\App\Models\Invite $invite)
    {

        $deleted = $this->shortLinkService->deleteByTargetInvite($invite->invite_code);
        if (!$deleted) {
            throw new ApiException('No short link found for this invitation, so it could not be deleted.', 422);
        }

        return true;

    }

    private function updateAgent(\App\Models\Invite $invite, $license_id): bool
    {


        $data = new UpdateAgentData(
            status: Agent::AGENT_STATUS_CANCELED
        );

        $updated = $this->agentService->updateAgentByUserID($license_id, $invite->invited_id, $data);

        if (!$updated) {
            throw new ApiException('The agent could not be canceled. It may not exist or already have the desired status.', 422);
        }

        return true;
    }
}