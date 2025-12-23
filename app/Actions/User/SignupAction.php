<?php

namespace App\Actions\User;

use App\Actions\Auth\CreateTokenAction;
use App\Actions\Company\CreateCompanyAction;

use App\Actions\Team\Invite\TeamInviteLinkValidateAction;
use App\Actions\Team\Invite\TeamInviteComplete;
use App\Models\Invite;
use App\Models\User;
use App\Services\InviteService;
use App\Services\UserService;
use DB;
use Throwable;
class SignupAction
{

    public function __construct(
        private UserService $userService,
        private InviteService $inviteService,
        private CreateTokenAction $createTokenAction,
        private CreateUserAction $createUserAction,
        private CreateCompanyAction $createCompanyAction,
        private TeamInviteLinkValidateAction $inviteValidateAction,
        private TeamInviteComplete $inviteComplete
    ) {

    }

    public function execute(array $data): array
    {

        try {


            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();


            $inviteCode = $data['invite_code'] ?? null;
            $invite = $inviteCode ? $this->inviteValidateAction->execute(['code' => $inviteCode] ?? null, true) : null;

            $email = $invite ? $invite->invited_email : trim($data['email']);

            $user = $this->createUserAction->execute(['fullname' => $data['fullname'], 'email' => $email, 'password' => $data['password']], $invite ? false : true);

            [$company, $licenseNumber] = $this->createCompanyAction->execute($user, $invite);

            $this->inviteComplete($invite, $user);



            $token = $this->createTokenAction->execute($user);


            DB::connection('mongodb')->commit();
            DB::commit();

            return [
                'token' => $token['access_token'],
                'user' => $user,
                'company' => $company,
                'licenseNumber' => $licenseNumber
            ];

        } catch (Throwable $e) {
            DB::connection('mongodb')->rollBack();
            DB::rollBack();

            throw $e;

        }
    }

    private function inviteComplete(?Invite $invite, User $user)
    {
        if (!empty($invite)) {
            $this->inviteComplete->execute($invite, $user);
        }

    }
}