<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserRepositoryImplement implements UserRepositoryInterface
{

    public function getAll($size = 6, $q = '') {
        return User::where('name', 'like', '%'.$q.'%')
                   ->orWhere('email', 'like', '%'.$q.'%')
                   ->select(['id', 'name','email', 'phone', 'role', 'created_at', 'deleted_at'])
                   ->orderBy('created_at', 'desc')
                   ->paginate($size);        
    }

    public function getById($id) {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create([
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => '0'.$data['phone'],
            'password' => $data['hashedPassword'],
        ]);
    }
    public function update($id, array $data) {}

    public function delete($id) {}

    
    public function getByEmail($email) {
        return User::where('email', '=', $email)->first();
    }
}
