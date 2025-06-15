<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Services\UserService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends ControllerWithGuard
{
    use APIResponse;
    private $userService;
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('admin');
        
        $size = request()->query('size', 6);
        $q = request()->query('q');
        $users = $this->userService->getAll($size, $q);
        return $this->responseSuccessWithData($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        Gate::authorize('admin');
        $data = $request->only(['id', 'email', 'name', 'phone', 'faculty']);
        $user = $this->userService->store(
            $data['id'],
            $data['email'],
            $data['name'],
            $data['phone'],
            $data['faculty'],
            $data['id']
        );
        return $this->responseSuccessWithData($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->userService->getById($id);
        if (!$user) {
            return $this->responseError('User not found', 404);
        }
        return json_encode($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Gate::authorize('admin');
        $data = $request->only(['name', 'phone', 'faculty']);
        $data['id'] = $id;
        $user = $this->userService->update($data);
        return $this->responseSuccessWithData($user, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function me() {
        $user = Auth::user();
        return $this->responseSuccessWithData($user);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $result = $this->userService->importUsersFromExcel($request->file('file'));

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['status']);
    }
}
