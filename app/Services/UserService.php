<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function store(String $email, String $name, String $phone, String $password)
    {
        return $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'hashedPassword' => Hash::make($password),
        ]);
    }
}
