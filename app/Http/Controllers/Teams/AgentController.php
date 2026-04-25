<?php

namespace App\Http\Controllers\Teams;

use App\Domain\Agent\Actions\DeleteAgentAction;
use App\Domain\Agent\Actions\SuspendAgentAction;
use App\Domain\Agent\Constants\Agent;
use App\Domain\Agent\DTO\UpdateAgentData;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\Agent\DeleteAgentRequest;
use App\Http\Requests\Teams\Agent\SuspendAgentRequest;
use App\Http\Requests\Teams\Agent\UpdateAgentRequest;
use App\Http\Resources\Agent\AgentsResource;
use App\Services\CompanyService;
use Illuminate\Validation\Rule;
use App\Domain\Agent\Services\AgentService;
use Throwable;
class AgentController extends Controller
{


    public function __construct(
        private readonly AgentService $agentService,
        private readonly DeleteAgentAction $deleteAgentAction,
        private readonly CompanyService $companyService,
        private readonly SuspendAgentAction $suspendAgentAction
    ) {
    }


    public function update(UpdateAgentRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        unset($validated['id']);

        $user = auth()->user();


        $agentData = new UpdateAgentData(
            away: $validated['away'] ?? null,
            chat_limit: $validated['chat_limit'] ?? null,
            agent_name: $validated['agent_name'] ?? null,
            job_title: $validated['job_title'] ?? null,
            auto_assign: $validated['auto_assign'] ?? null,
            department_ids: $validated['department_ids'] ?? null
        );

        $update = $this->agentService->update(
            $user->license,
            $agentData,
            [
                'id' => $id,
                'status' => Agent::AGENT_STATUS_ACTIVE
            ]
        );

        if (!$update) {
            throw new ApiException('No changes detected. Agent was not updated.', 400);
        }


        $agent = AgentsResource::collection(
            $this->companyService->enrichAgentsWithUserAndInvite(
                $user->company,
                collect([$update])
            )
        );


        return response()->success([
            'agent' => $agent[0] ?? []
        ], "Agent has been successfully updated.");
    }

    public function destroy(DeleteAgentRequest $request, string $id)
    {

        $id = $request->validated()['id'];

        try {
            $this->deleteAgentAction->execute($id);
            return response()->success([
                'id' => $id
            ]);
        } catch (Throwable $e) {
            throw new ApiException('The agent could not be deleted.', 422);
        }


    }

    public function suspend(SuspendAgentRequest $request)
    {

        $id = $request->validated()['id'];

        try {
            $user = auth()->user();
            $agent = $this->suspendAgentAction->execute($user->license, $id);

            $collection = $this->companyService
                ->enrichAgentsWithUserAndInvite(
                    $user->company,
                    collect([$agent])
                )
                ->first();

            $agent = new AgentsResource($collection);

            return response()->success(['agent' => $agent]);

        } catch (Throwable $e) {

            throw new ApiException('Agent could not be suspended/activated, please try again.', 422);
        }


    }


}
