<?php

namespace App\Actions\User;

use App\DTO\User\UpdateUserData;
use App\Models\User;
use App\Services\UserService;
use App\Exceptions\ApiException;
use App\Constants\YesNo;
use App\Services\Verification\EmailVerificationService;
use App\DTO\User\CreateUserData;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UpdateUserAction
{

    public function __construct(private UserService $userService, private EmailVerificationService $verify)
    {
    }

    public function execute(array $data): ?User
    {

        $data = $this->validateField($data);
        if (empty($data)) {
            return null;
        }

        $userData = new UpdateUserData(
            full_name: $data['name'] ?? null,
        );

        return $this->userService->update(auth()->user(), $userData);


    }

    private function validateField(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:25|min:5',

        ], [

        ], [
            'name' => 'First Last Name'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), 400);
        }

        return $validator->validated();

    }
}