<?php

namespace App\Domain\Invite\Actions;

use App\Domain\Invite\Actions\TeamInviteComplete;
use App\Domain\Invite\Actions\TeamInviteLinkValidateAction;
use App\Exceptions\ApiException;
use App\Models\Invite;
use App\Services\UserService;
use Throwable;
use DB;

class TeamInviteAcceptAction
{



    public function __construct(
        readonly private TeamInviteLinkValidateAction $inviteValidateAction,
        readonly private UserService $userService,
        readonly private TeamInviteComplete $inviteComplete
    ) {
    }
    public function execute(string $code)
    {

        try {


            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();

            $invite = $this->validate($code);
            $this->userService->attachCompany($invite->user, $invite->company->id, ['role' => $invite->invited_role]);
            $this->inviteComplete->execute($invite, $invite->user);

            DB::connection('mongodb')->commit();
            DB::commit();

            return [
                'user' => $invite->user
            ];

        } catch (Throwable $e) {
            report($e);
            DB::connection('mongodb')->rollBack();
            DB::rollBack();

            throw $e;
        }
    }


    private function validate(string $code): ?Invite
    {
        $invite = $this->inviteValidateAction->execute(['code' => $code], true);
        if (!$invite->user->id) {
            throw new ApiException('We couldn’t complete your request to join the company.', 422);

        }

        return $invite;
    }
}