<?php

namespace App\Imports;

use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function chunkSize(): int
    {
        return 1000; // Xử lý 1000 dòng mỗi lần
    }
    public function model(array $row)
    {
        Log::info($row['id']);
        // Kiểm tra nếu ID đã tồn tại thì bỏ qua
        if ($this->userRepository->getById($row['id'])) {
            return null;
        }

        if ($row['id']) {
            return $this->userRepository->create([
                'id' => $row['id'],
                'name' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['id'] . '@sv1.dut.udn.vn',
                'faculty' => $row['faculty'],
                'hashedPassword' => Hash::make($row['id']),
            ]);
        }
        return null;
    }
}
