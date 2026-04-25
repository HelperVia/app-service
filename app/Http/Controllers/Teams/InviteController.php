<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Auth\AuthDataAction;
use App\Actions\Auth\CreateTokenAction;
use App\Domain\Invite\Actions\TeamInviteAcceptAction;
use App\Domain\Invite\Actions\TeamInviteAction;
use App\Domain\Invite\Actions\TeamInviteLinkValidateAction;
use App\Domain\Invite\Actions\TeamInviteValidateAction;
use App\Domain\Invite\Factory\CancelInviteFactory;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\Agent\Invite\CancelInviteRequest;
use App\Http\Resources\Agent\AgentsResource;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use App\Domain\Agent\Constants\Agent;
use Illuminate\Validation\Rule;
use Throwable;

class InviteController extends Controller
{

    public function __construct(
        private readonly TeamInviteValidateAction $inviteValidateActionAction,
        private readonly TeamInviteAction $inviteAction,
        private readonly TeamInviteLinkValidateAction $inviteLinkValidateAction,
        private readonly TeamInviteAcceptAction $inviteAcceptAction,
        private readonly CompanyService $companyService,
        private readonly CancelInviteFactory $cancelInviteFactory,
        private readonly CreateTokenAction $createTokenAction,
        private readonly AuthDataAction $authDataAction
    ) {
    }
    public function create(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => [
                'required',
                Rule::in([
                    Agent::AGENT_ROLE_AGENT,
                    Agent::AGENT_ROLE_SUPERADMIN
                ])
            ]
        ]);


        try {
            $user = auth()->user();
            $response = $this->inviteAction->execute($validated, $user, $user->company);
            $collection = $this->companyService
                ->enrichAgentsWithUserAndInvite(
                    $user->company,
                    collect([$response['agent']])
                )
                ->first();

            $agent = new AgentsResource($collection);
            return response()->success(['agent' => $agent]);

        } catch (Throwable $e) {
            throw new ApiException('Invitation could not be sent.', 422);
        }


    }
    public function validateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $company = auth()->user()->company;
        $result = $this->inviteValidateActionAction->execute($validated['email'], $company);

        return response()->success($result);
    }

    public function validateLink(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required'
        ]);

        $data = $this->inviteLinkValidateAction->execute($validated);

        return response()->success($data);
    }

    public function accept(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required'
        ]);

        try {
            $response = $this->inviteAcceptAction->execute($validated['code']);

            $user = $response['user'];

            $token = $this->createTokenAction->execute($user);

            return response()->success($this->authDataAction->execute($user, $token['access_token']));

        } catch (Throwable $e) {

            throw new ApiException('We couldn’t complete your request to join the company.', 422);

        }

    }

    public function cancel(CancelInviteRequest $request)
    {

        $id = $request->validated()['id'];

        try {
            $strategy = $this->cancelInviteFactory->make('agent');
            $strategy->execute($id, auth()->user()->license);
            return response()->success(true);
        } catch (Throwable $e) {

            throw new ApiException('We could not cancel your invitation at this time. Please try again later.', 422);

        }

    }
}
