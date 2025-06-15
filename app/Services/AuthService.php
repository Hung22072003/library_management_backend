<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Repositories\User\UserRepositoryInterface;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    private $userRepository;
    use APIResponse;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function sendOtp($email)
    {
        $user = $this->userRepository->getByEmail($email);
        if (!$user) return $this->responseError('Email not found', 404);

        $otp = rand(100000, 999999);
        OtpCode::updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'expired_at' => now()->addMinutes(1)
            ]
        );

        // Gửi email
        Mail::to($email)->queue(new OtpMail($user->name, $otp));

        return $this->responseSuccess('OTP sent');
    }

    public function verifyOtp($email, $otp)
    {
        $record = OtpCode::where('email', $email)
                    ->where('otp', $otp)
                    ->first();

        if (!$record) {
            return $this->responseError('Invalid OTP', 400);
        }

        if ($record->expired_at < now()) {
            $record->delete();
            return $this->responseError('OTP expired', 400);
        }
        $record->delete();
        return $this->responseSuccess('OTP verified');
    }

    public function resetPassword($data)
    {
        $user = $this->userRepository->getByEmail($data['email']);
        $user->password = Hash::make($data['password']);
        $user->save();

        return $this->responseSuccess('Password reset successful');
    }
}
