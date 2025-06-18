<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Services\AuthService;
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
    private $authService;
    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
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
        $user = $this->userService->getByEmail($request->get('email'));
        if($user && $user->deleted_at) return $this->responseError('User is deleted', 500);
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
        $user = $this->userService->store($request->id, $request->email, $request->name, $request->phone, $request->password, $request->password);
        $access_token = JWTAuth::fromUser($user);
        $result = [
            'user' => $user,
            'access_token' => $access_token
        ];

        return $this->responseSuccessWithData($result, true);
    }

     public function requestOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return $this->authService->sendOtp($request->email);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string'
        ]);
        return $this->authService->verifyOtp($request->email, $request->otp);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        return $this->authService->resetPassword($request->all());
    }
}
