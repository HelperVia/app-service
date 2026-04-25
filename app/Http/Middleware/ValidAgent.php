<?php

namespace App\Http\Middleware;

use App\Domain\Agent\Services\AgentService;
use App\Exceptions\ApiException;
use App\Repositories\CompanyRepository;
use App\Services\CompanyService;
use App\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function __construct(
        readonly private AgentService $agentService,
        readonly private CompanyService $companyService
    ) {
    }
    public function handle(Request $request, Closure $next): Response
    {

        $user = auth()->user();
        $agentId = $this->agentService->isValidAgentByUserID($user->license, $user->id);
        if (!$agentId) {
            return response()->json(
                [
                    'message' => "Agent not active or does not exist",
                    'step' => 'companies',
                ],
                403
            );
        }

        $user->agent_id = (string) $agentId;
        auth()->setUser($user);

        return $next($request);


    }
}
