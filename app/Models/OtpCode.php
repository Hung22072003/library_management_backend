<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;
    const EMAIL = 'email';
    const OTP = 'otp';
    const EXPIRED_AT = 'expired_at';
    protected $fillable = [
        self::EMAIL,
        self::OTP,
        self::EXPIRED_AT,
    ];
}
