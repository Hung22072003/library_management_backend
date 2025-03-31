<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserRepositoryImplement implements UserRepositoryInterface
{

    public function getAll() {}

    public function getById($id) {}

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['hashedPassword'],
        ]);
    }
    public function update($id, array $data) {}

    public function delete($id) {}
}
