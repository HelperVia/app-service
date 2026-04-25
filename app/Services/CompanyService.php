<?php

namespace App\Services;
use App\Constants\YesNo;
use App\Domain\Agent\Services\AgentService;
use App\Http\Resources\Company\UsersResource;
use App\Models\Companies;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
class CompanyService
{



    public function __construct(private CompanyRepository $companyRepository, private AgentService $agentService)
    {


    }
    public function createLicenseNumber(): int
    {
        do {
            $rand = rand(11111111, 99999999);
        } while ($this->companyRepository->doesLicenseNumberExist($rand));

        return $rand;

    }

    public function create(array $data): Companies
    {

        return $this->companyRepository->create([
            'company_name' => $data['name'],
            'license_number' => $data['license_number'],
            'create_step' => $data['step'],
            'status' => $data['status']
        ]);
    }

    public function resolveLicenseNumber($invite)
    {
        return $invite ? $invite->company->license_number : $this->createLicenseNumber();
    }



    public function getUsers(Companies $companies)
    {
        return $this->companyRepository->getUsers($companies);
    }

    public function enrichAgentsWithUserAndInvite(Companies $companies, \Illuminate\Support\Collection $agents)
    {
        $userIds = $agents->pluck('user_id')->unique()->toArray();
        $users = $this->companyRepository->getUsersByIds($companies, $userIds);
        $usersByID = $users->keyBy('id');
        $missingUserIds = collect($userIds)
            ->diff($usersByID->keys())
            ->values()
            ->toArray();

        $invites = $this->companyRepository->getInviteByInvitedIds($companies, $missingUserIds);

        $invitesByUserId = $invites->keyBy('invited_id');

        return $agents->map(function ($agent) use ($usersByID, $invitesByUserId) {
            if ($usersByID->has($agent->user_id)) {
                $user = $usersByID->get($agent->user_id);
                $agent->email = $user->email;
                $agent->agent_name = $agent->agent_name ?? $user->full_name;
                $agent->source = 'user';
                return $agent;
            }
            if ($invitesByUserId->has($agent->user_id)) {
                $invite = $invitesByUserId->get($agent->user_id);
                $agent->invited = YesNo::YES;
                $agent->email = $invite->invited_email;
                $agent->agent_name = $agent->agent_name ?? $invite->temporary_name;
                $agent->source = 'invite';
                return $agent;

            }
            return null;

        })->filter()->values();
    }
    public function getTeams(Companies $companies)
    {

        $agents = $this->agentService->getAgents($companies->license_number);

        return $this->enrichAgentsWithUserAndInvite($companies, $agents);
    }

    public function findCompanyByLicenseNumber(string $license_number): ?Companies
    {
        return $this->companyRepository->findCompanyByLicenseNumber($license_number);
    }
    public function isValidLicense(string $license)
    {
        return $this->findCompanyByLicenseNumber($license);
    }


    public function getUserByEmail(Companies $companies, string $email): ?User
    {
        return $this->companyRepository->getUserByEmail($companies, $email);
    }


}