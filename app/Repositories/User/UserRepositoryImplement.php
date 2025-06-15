<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepositoryImplement implements UserRepositoryInterface
{

    public function getAll($size = 6, $q = '') {
        return User::where('name', 'like', '%'.$q.'%')
                   ->orWhere('id', 'like', '%'.$q.'%')
                   ->select(['id', 'name','email', 'phone', 'faculty', 'role', 'created_at', 'deleted_at'])
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
            'phone' => strlen($data['phone']) == 10 ? $data['phone'] : '0'.$data['phone'],
            'faculty' => $data['faculty'],
            'password' => $data['hashedPassword'],
        ]);
    }
    public function update($id, array $data) {
        return DB::transaction(function () use ($id, $data){
            $user = User::findOrFail($id);
            $user->update([
                USER::NAME => $data[USER::NAME],
                USER::PHONE => $data[USER::PHONE],
                USER::FACULTY => $data[USER::FACULTY]
            ]);
            return $user;
        });
    }

    public function delete($id) {}

    
    public function getByEmail($email) {
        return User::where('email', '=', $email)->first();
    }
}
