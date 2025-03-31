<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use APIResponse;
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Handle user registration.
     */
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // => get the current authenticated user

            try {
                $access_token = JWTAuth::fromUser($user);

                return $this->responseSuccessWithData([
                    'user' => $user,
                    'access_token' => $access_token
                ]);
            } catch (JWTException $e) {
                return $this->responseError('Could not create token', 500);
            }
        }

        return $this->responseError('Unauthorized', 401);
    }

    public function register(UserRegisterRequest $request)
    {
        $user = $this->userService->store($request->email, $request->name, $request->phone, $request->password);
        $access_token = JWTAuth::fromUser($user);
        $result = [
            'user' => $user,
            'access_token' => $access_token
        ];

        return $this->responseSuccessWithData($result, true);
    }
}
