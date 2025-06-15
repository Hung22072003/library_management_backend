<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class MaxLoanPeriod implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Lấy dữ liệu đầu vào từ request
        $data = request()->all();

        // Kiểm tra sự tồn tại của 'borrowed_at' và 'due_date'
        if (!isset($data['borrowed_at']) || !isset($data['due_date'])) {
            $fail('Required borrowed_at and due_date.');
            return;
        }

        // Chuyển đổi borrowed_at và due_date thành đối tượng Carbon
        $borrowedAt = Carbon::createFromFormat('Y-m-d', $data['borrowed_at']);
        $dueDate = Carbon::createFromFormat('Y-m-d', $data['due_date']);

        // Tính số ngày giữa borrowed_at và due_date
        $daysDifference = $borrowedAt->diffInDays($dueDate);

        // Kiểm tra nếu số ngày vượt quá 30
        if ($daysDifference > 30) {
            $fail('Book loan period cannot exceed 30 days.');
        }
    }
}
