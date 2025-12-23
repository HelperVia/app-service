<?php
namespace App\Repositories;
use App\Models\Companies;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
class CompanyRepository
{


    public function __construct(private Companies $model)
    {
    }
    public function doesLicenseNumberExist($license_number)
    {
        return $this->model->firstWhere('license_number', $license_number);
    }

    public function create(array $data): Companies
    {
        return $this->model->create($data);
    }
    public function getUsers(Companies $companies)
    {
        return $companies->users()->get();
    }

    public function findCompanyByLicenseNumber(string $license_number): ?Companies
    {
        return $this->model->firstWhere('license_number', $license_number);
    }
    public function getUsersByIds(Companies $companies, array $userIds)
    {
        return $companies->users()->select('users.id', 'users.email', 'users.full_name')->whereIn('users.id', $userIds)->get();

    }
    public function getInviteByInvitedIds(Companies $companies, array $invitedIds)
    {
        return $companies->invites()->whereIn('invited_id', $invitedIds)->get();
    }
    public function getUserByEmail(Companies $companies, string $email): ?User
    {
        return $companies->users()->where('email', $email)->first();
    }

}