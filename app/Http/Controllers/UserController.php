<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
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
