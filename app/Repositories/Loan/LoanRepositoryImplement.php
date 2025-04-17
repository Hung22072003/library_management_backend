<?php

namespace App\Repositories\Loan;

use App\Models\BookLoansBatch;
use App\Models\BookLoansDetail;
use App\Repositories\Loan\LoanRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanRepositoryImplement implements LoanRepositoryInterface
{
    public function getAll($size = 6, $q = '')
    {
        return BookLoansBatch::with('loanDetails')->where('user_id', 'like', '%' . $q . '%')->orderBy('id', 'desc')->paginate($size);
    }

    public function getBatchesOfUser($id, $size = 6) {
        return BookLoansBatch::where('user_id', '=' , $id)->with('loanDetails')->orderBy('id', 'desc')->paginate($size);
    }
    public function getById($id)
    {
        return BookLoansBatch::with('loanDetails.book')->find($id);
    }
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $borrowedAtFormatted = Carbon::parse($data['borrowed_at'])->format('Y-m-d');
            $expiredAt = Carbon::parse($borrowedAtFormatted)->addDays(2)->format('Y-m-d');
            $loanBatch = BookLoansBatch::create([
                BookLoansBatch::BORROWED_AT => $data['borrowed_at'],
                BookLoansBatch::DUE_AT => $data['due_date'],
                BookLoansBatch::USER_ID => $data['user_id'],
                BookLoansBatch::EXPIRED_AT =>  $expiredAt
            ]);

            foreach ($data['carts'] as $cart) {
                BookLoansDetail::create([
                    'batch_id' => $loanBatch->id,
                    'book_id' => $cart->book_id,
                    'rental_fee' => $cart->rental_fee
                ]);
            }

            return $loanBatch;
        });
    }
    public function update($id, array $data) {}
    public function delete($id) {}

    public function getExpiredPendingLoans()
    {
        return BookLoansBatch::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->get();
    }

    public function getOverdueLoans()
    {
        return BookLoansBatch::where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->get();
    }
    public function cancelLoanBatch(BookLoansBatch $loan)
    {
        $loan->update(['status' => 'cancel']);
        $loan->loanDetails()->update(['borrowed_status' => 'cancel']);
    }

    public function overdueLoanBatch(BookLoansBatch $loan)
    {
        $loan->update(['status' => 'overdue']);
        $loan->loanDetails()->update(['borrowed_status' => 'overdue']);
    }

    public function returnLoanBatch(BookLoansBatch $batch)
    {
        $now = Carbon::now();
        $batch->update([
            BookLoansBatch::RETURN_AT => $now,
            BookLoansBatch::STATUS => $now->lessThanOrEqualTo($batch->due_at) ? 'returned' : 'returned (late)'
        ]);

        $batch->loanDetails()->update([
            'returned_at' => $now,
            'borrowed_status' => $batch->status,
        ]);
    }

    public function borrowLoanBatch(BookLoansBatch $loan)
    {
        $loan->update(['status' => 'borrowed']);
        $loan->loanDetails()->update(['borrowed_status' => 'borrowed']);
    }

    public function extendLoanBatch(BookLoansBatch $loan, $date)
    {
        $loan->update([BookLoansBatch::EXTENDED_AT => $date]);
    }

    public function updateReturnDetails(BookLoansBatch $batch, array $returnDetails)
    {
        $now = Carbon::now();
        $status = $now->lessThanOrEqualTo($batch->due_at) ? 'returned' : 'returned (late)';
        $batch->update([
            BookLoansBatch::RETURN_AT => $now,
            BookLoansBatch::STATUS => $status
        ]);

        foreach ($returnDetails as $detail) {
            $batch->loanDetails()
                ->where('book_id', $detail['book_id'])
                ->update([
                    'note' => $detail['note'],
                    'returned_condition' => $detail['returned_condition'],
                    'returned_at' => $now,
                    'borrowed_status' => $status,
                ]);
        }
    }
}
