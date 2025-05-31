<?php

namespace App\Services;

use App\Imports\UsersImport;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UserService
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function store(String $id, String $email, String $name, String $phone, String $password)
    {
        return $this->userRepository->create([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'hashedPassword' => Hash::make($password),
        ]);
    }
    public function importUsersFromExcel($file)
    {
        try {
            Log::info('User import started', ['file' => $file->getClientOriginalName()]);
            Excel::import(new UsersImport($this->userRepository), $file);
            return [
                'status' => 200,
                'message' => 'Users imported successfully!',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Error importing users: ' . $e->getMessage(),
            ];
        }
    }
    public function getAll($size, $q) {
        return $this->userRepository->getAll($size, $q);
    }

    public function getByEmail($email) {
        return $this->userRepository->getByEmail($email);
    }
    
    public function getById($id) {
        return $this->userRepository->getById($id);
    }
}
