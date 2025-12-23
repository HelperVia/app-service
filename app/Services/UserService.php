<?php

namespace App\Services;


use App\DTO\User\CreateUserData;
use App\DTO\User\UpdateUserData;
use App\Http\Resources\User\CompanyResource;
use App\Models\User;
use App\Services\InviteService;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserService
{


    public function __construct(
        private UserRepository $userRepository,

    ) {
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }
    public function create(CreateUserData $data): User
    {
        return $this->userRepository->create($data->toArray());
    }
    public function attachCompany($user, $company_id, $data)
    {
        return $this->userRepository->attachCompany($user, $company_id, $data);
    }
    public function isEmailUsed(string $email): bool
    {
        return $this->getUserByEmail($email) ? true : false;
    }

    public function update(User $user, UpdateUserData $data): ?User
    {

        return $this->userRepository->update($user, $data->toArray());

    }

    public function isValidLicense(?string $license, User $user): bool
    {
        if (!$license) {
            return false;
        }
        return $this->userRepository->hasValidLicense($license, $user);
    }
    public function getCompanies(User $user): ResourceCollection
    {

        $companiesResources = $this->userRepository->getCompanies($user);
        return CompanyResource::collection($companiesResources);
    }


    public function getAuthenticatedUser(): ?User
    {
        return auth()->user();
    }



}