<?php

namespace App\Repositories;
use App\Models\Companies;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
class UserRepository
{

    public function __construct(private User $model)
    {
    }

    public function findUserByEmail(string $email): ?User
    {

        return $this->model->firstWhere('email', $email);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);

    }
    public function attachCompany(User $user, string $company_id, array $data)
    {
        return $user->companies()->attach($company_id, $data);
    }

    public function update(User $user, $data): ?User
    {
        $user->fill($data);

        if ($user->isDirty()) {
            $user->save();
            return $user->fresh();
        }

        return null;

    }




    public function getLicense(string $license, User $user)
    {

        return $user->companies()->where('license_number', $license);
    }

    public function hasValidLicense(string $license, User $user): bool
    {
        return $this->getLicense($license, $user)->exists();
    }

    public function getValidLicense(string $license, User $user)
    {
        return $this->getLicense($license, $user)->first();
    }
    public function getCompanies(User $user): Collection
    {
        return $user->companies()->get();
    }

}