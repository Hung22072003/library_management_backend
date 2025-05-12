<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use Illuminate\Support\Carbon;

class TransactionRepositoryImplement implements TransactionRepositoryInterface
{

    public function getAll($size = 6, $q = '') {
        return Transaction::where('user_id', 'like', '%'.$q.'%')->orderBy('id', 'desc')->paginate($size);
    }

    
    public function getTransactionsOfUser($id, $size = 6, $q = '') {
        return Transaction::where('user_id', $id)->where('note', 'like', '%'.$q.'%')->orderBy('id', 'desc')->paginate($size);
    }

    public function getById($id) {
        return Transaction::with('user')->find($id);
    }

    public function create(array $data)
    {
        return Transaction::create([
            Transaction::COPY_ID => $data['copy_id'],
            Transaction::AMOUNT => $data['amount'],
            Transaction::TYPE => $data['type'],
            Transaction::NOTE => $data['note'],
            Transaction::USER_ID => $data['user_id'],
            Transaction::BATCH_ID => $data['batch_id'],
            Transaction::PAYMENT_EXPIRED_AT => Carbon::now()->addDays(2)
        ]);
    }
    public function update($id, array $data) {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            Transaction::PAYMENT_STATUS => $data['payment_status'],
            Transaction::PAYMENT_METHOD => $data['payment_method'],
        ]);
    }

    public function delete($id) {}

    public function getTransactionsOfLoanBatch($id) {
        return Transaction::where('batch_id', $id)->get();
    }
}
