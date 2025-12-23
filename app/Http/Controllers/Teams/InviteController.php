<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Team\Invite\TeamInviteAction;
use App\Actions\Team\Invite\TeamInviteLinkValidateAction;
use App\Actions\Team\Invite\TeamInviteValidateAction;
use App\Http\Controllers\Controller;
use App\Services\InviteService;
use Illuminate\Http\Request;
use App\Constants\Agent;
use Illuminate\Validation\Rule;

class InviteController extends Controller
{

    public function __construct(
        private readonly InviteService $inviteService,
        private readonly TeamInviteValidateAction $inviteValidateActionAction,
        private readonly TeamInviteAction $inviteAction,
        private readonly TeamInviteLinkValidateAction $inviteLinkValidateAction
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
        $data = $this->inviteAction->execute($validated);

        return response()->success($data);
    }
    public function validateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->inviteValidateActionAction->execute($validated['email']);

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
}
