<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const ID = 'id';
    const EMAIL = 'email';
    const NAME = 'name';
    const PASSWORD = 'password';
    const ROLE = 'role';
    const PHONE = 'phone';
    const FACULTY = 'faculty';
    const ROLE_ADMIN = 'ADMIN';

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        self::ID,
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::ROLE,
        self::PHONE,
        self::FACULTY
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::PASSWORD,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    // public function serializeDate(\DateTimeInterface $date)
    // {
    //     return $date->setTimezone('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
    // }

    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }

    public function isUser()
    {
        return $this->role === 'USER';
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function chatbotLogs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function bookLoansBatches(): HasMany
    {
        return $this->hasMany(BookLoansBatch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }
}
