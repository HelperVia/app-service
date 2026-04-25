<?php

namespace App\Actions\User;

use App\Services\UserService;
use App\Exceptions\ApiException;
use App\Constants\YesNo;
use App\Services\Verification\EmailVerificationService;
use App\DTO\User\CreateUserData;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CreateUserAction
{

    public function __construct(private UserService $userService, private EmailVerificationService $verify)
    {
    }

    public function execute(array $data, $verify = false)
    {

        $this->validateField($data);

        $email = $data['email'];
        $password = $data['password'];
        $fullname = $data['fullname'];
        $isEmailUsed = $this->userService->isEmailUsed($email);
        if ($isEmailUsed) {
            throw new ApiException('E-mail Address is Used.', 400);
        }

        $userData = new CreateUserData(
            email: $email,
            full_name: $fullname,
            password: $password,
            email_verification: $verify ? YesNo::NO : YesNo::YES
        );
        return $this->userService->create($userData);
    }

    private function validateField(array $data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ], [
            'email.required' => 'E-mail Address cannot be empty.',
            'email.email' => 'E-mail Address must be a valid email.',
            'email.unique' => 'E-mail Address is already in use.',
            'password.required' => 'Password cannot be empty'
        ]);

        if ($validator->fails()) {
            throw new ApiException($validator->errors()->first(), 400);
        }

    }
}