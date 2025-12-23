<?php

namespace App\Http\Controllers\User;


use App\Actions\User\UpdateUserAction;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;




class UserController extends Controller
{



    public function __construct(
        private UserService $userService,
        private UpdateUserAction $updateUserAction
    ) {
    }


    public function updateSettings(Request $request)
    {


        $result = $this->updateUserAction->execute($request->post());



        if ($result) {
            return response()->success([], 'User updated successfully.');
        }

        throw new ApiException('User update failed.', 409);

    }

    public function companies(Request $request)
    {

        $user = auth()->user();
        $companies = $this->userService->
            getCompanies($user)
            ->toArray($request);
        return response()->success([
            'user' => $user->full_name,
            'companies' => $companies
        ]);
    }



}
