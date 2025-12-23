<?php

namespace App\Http\Controllers\Teams;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Services\AgentService;
use Illuminate\Http\Request;
use App\Constants\YesNo;
class AgentController extends Controller
{


    public function __construct(private readonly AgentService $agentService)
    {
    }


    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'away' => ['sometimes', 'required', Rule::in([YesNo::YES, YesNo::NO])],
            'chat_limit' => 'sometimes|required|integer|between:0,50'

        ], [], [
            'away' => 'Away',
            'chat_limit' => 'Chat Limit'
        ]);


        $update = $this->agentService->update(auth()->user()->license, $validated, $id);

        if (!$update) {
            throw new ApiException('No changes detected. User was not updated.', 400);
        }

        return response()->success([
            'id' => $update->id
        ]);
    }


}
