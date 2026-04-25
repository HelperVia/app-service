<?php

namespace App\Http\Controllers\Teams;

use App\Domain\Department\Actions\CreateAction;
use App\Domain\Department\Actions\DeleteAction;
use App\Domain\Department\Actions\UpdateAction;
use App\Domain\Department\Services\DepartmentService;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\Department\CreateDepartmentRequest;
use App\Http\Requests\Teams\Department\DeleteDepartmentRequest;
use App\Http\Requests\Teams\Department\UpdateDepartmentRequest;
use App\Http\Resources\Department\TeamDepartmentResource;
use Illuminate\Http\Request;
use Throwable;

class DepartmentController extends Controller
{

    public function __construct(
        private readonly CreateAction $createAction,
        private readonly DeleteAction $deleteAction,
        private readonly DepartmentService $departmentService,
        private readonly UpdateAction $updateAction,
    ) {
    }


    public function update(UpdateDepartmentRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        unset($validated['id']);


        try {
            $user = auth()->user();
            $this->updateAction->execute($user->license, $id, $validated);

            $departments = TeamDepartmentResource::collection(collect($this->departmentService->getWithAgents($user->license, [$id])));
            return response()->success([
                'department' => $departments[0] ?? []
            ]);

        } catch (Throwable $e) {

            throw new ApiException("Department could not be updated");
        }



    }
    public function destroy(DeleteDepartmentRequest $request)
    {

        $id = $request->validated()['id'];
        try {

            $this->deleteAction->execute($id, auth()->user()->license);
            return response()->success('Department deleted successfully');

        } catch (Throwable $e) {
            throw new ApiException($e->getMessage());
        }


    }
    public function store(CreateDepartmentRequest $request)
    {

        $validated = $request->validated();
        try {
            $user = auth()->user();
            $created_department = $this->createAction->execute($validated, $user->license);
            $department = TeamDepartmentResource::collection(collect($this->departmentService->getWithAgents($user->license, [$created_department['id']])));
            return response()->success([
                'department' => $department[0] ?? []
            ]);

        } catch (Throwable $e) {
            throw new ApiException('Failed to add agent to the department.');
        }


    }
}
