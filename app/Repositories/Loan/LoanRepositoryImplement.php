<?php

namespace App\Repositories\Loan;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookCopyConditions;
use App\Models\BookLoansBatch;
use App\Models\BookLoansDetail;
use App\Models\Transaction;
use App\Repositories\Loan\LoanRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanRepositoryImplement implements LoanRepositoryInterface
{
    public function getAll($size = 6, $q = '')
    {
        return BookLoansBatch::with('loanDetails')->where('user_id', 'like', '%' . $q . '%')->orderBy('id', 'desc')->paginate($size);
    }

    public function getBatchesOfUser($id, $size = 6)
    {
        return BookLoansBatch::where('user_id', '=', $id)->with('loanDetails')->orderBy('id', 'desc')->paginate($size);
    }
    public function getById($id)
    {
        return BookLoansBatch::with('loanDetails.book', 'transactions')->find($id);
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
                    'copy_id' => $cart->copy_id,
                    'borrowed_at' => $data['borrowed_at'],
                    'due_at' => $data['due_date'],
                    'expired_at' => $expiredAt,
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
            ->where('expired_at', '<', today())
            ->get();
    }

    public function getOverdueLoans()
    {
        return BookLoansBatch::where('status', 'borrowed')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('due_at', '<', today())
                        ->whereNull('extended_at');
                })->orWhere(function ($q) {
                    $q->where('extended_at', '<', today());
                });
            })
            ->get();
    }

    public function getNearlyOverdueLoans()
    {
        return BookLoansBatch::where('status', 'borrowed')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereDate('due_at', '=', today())
                        ->whereNull('extended_at');
                })->orWhere(function ($q) {
                    $q->whereDate('extended_at', '=', today());
                });
            })
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
        foreach ($loan->loanDetails()->where('borrowed_status', 'borrowed')->get() as $item) {
            $item->borrowed_status = 'overdue';
            $item->save();
        }
    }

    public function returnLoanBatch(BookLoansBatch $batch)
    {
        $now = Carbon::now();
        $batch->update([
            BookLoansBatch::RETURN_AT => $now,
            BookLoansBatch::STATUS => $now->lessThanOrEqualTo($batch->due_at) ? 'returned' : 'returned (late)'
        ]);

        $batch->loanDetails()->update([
            'borrowed_status' => $batch->status,
            'return_at' => $now,
        ]);
    }

    public function borrowLoanBatch(BookLoansBatch $loan)
    {
        $loan->update(['status' => 'borrowed']);
        $loan->loanDetails()->where('borrowed_status', 'pending')->update(['borrowed_status' => 'borrowed']);
    }

    public function extendLoanBatch(BookLoansBatch $loan, $date)
    {
        $loan->update([BookLoansBatch::EXTENDED_AT => $date]);
        $loan->loanDetails()->update([
            "extended_at" => $date,
        ]);
    }

    public function updateReturnDetails(BookLoansBatch $batch, array $returnDetails)
    {
        $now = Carbon::now();
        $status = $now->lessThanOrEqualTo($batch->extended_at ?? $batch->due_at) ? 'returned' : 'returned (late)';
        $batch->update([
            BookLoansBatch::RETURN_AT => $now,
            BookLoansBatch::STATUS => $status
        ]);

        if (!empty($returnDetails)) {
            foreach ($returnDetails as $detail) {
                $batch->loanDetails()
                    ->where('book_id', $detail['book_id'])
                    ->update([
                        'note' => $detail['note'],
                        'returned_condition' => $detail['returned_condition'],
                        'borrowed_status' => $status,
                        'return_at' => $now,
                    ]);

                $updateData = [];
                if ($detail['returned_condition'] == 'lost') {
                    $updateData[BookCopy::STATUS] = 'unavailable';
                    Book::where('id', $detail['book_id'])->decrement('available_copies');
                }

                if ($detail['returned_condition'] != 'good') {
                    $updateData[BookCopy::CONDITION] = $detail['returned_condition'];
                    BookCopyConditions::create([
                        BookCopyConditions::COPY_ID => $detail['copy_id'],
                        BookCopyConditions::USER_ID => $batch->user_id,
                        BookCopyConditions::BATCH_ID => $batch->id,
                        BookCopyConditions::CONDITION_NOTE => $detail['note'],
                    ]);
                }

                BookCopy::where('id', $detail['copy_id'])->update($updateData);
            }
        }


        if ($status == 'returned (late)') {
            $lateFeePerDay = 5000;
            $totalAmount = array_reduce($batch->loanDetails->toArray(), function ($carry, $detail) use ($lateFeePerDay) {
                if ($detail['borrowed_status'] === 'returned (late)') {
                    $dueAt = Carbon::parse($detail['extended_at'] ?? $detail['due_at']);
                    $lateDays = $dueAt->diffInDays(Carbon::parse($detail['return_at']));
                    Log::info('Late days: ' . $lateDays);
                    return $carry + $lateFeePerDay * $lateDays;
                }
                return $carry;
            }, 0);


            Transaction::create([
                Transaction::NOTE => 'Thanh toán phí trễ hạn',
                Transaction::AMOUNT => $totalAmount,
                Transaction::TYPE => 'late_fee',
                Transaction::PAYMENT_EXPIRED_AT => $now->copy()->addDays(2),
                Transaction::BATCH_ID => $batch->id,
                Transaction::USER_ID => $batch->user_id,
            ]);
        }
    }

    public function returnOneBook($detail_id, $note, $returnedCondition)
    {
        $now = Carbon::now();
        $detail = BookLoansDetail::findOrFail($detail_id)->load('batch');
        $status = $now->lessThanOrEqualTo($detail->extended_at ?? $detail->due_at) ? 'returned' : 'returned (late)';
        $detail->update([
            'note' => $note,
            'returned_condition' => $returnedCondition,
            'borrowed_status' => $status,
            'return_at' => $now,
        ]);

        $updateData = [];
        if ($returnedCondition == 'lost') {
            $updateData[BookCopy::STATUS] = 'unavailable';
            Book::where('id', $detail->book_id)->decrement('available_copies');
        }


        if ($returnedCondition != 'good') {
            $updateData[BookCopy::CONDITION] = $returnedCondition;
            BookCopyConditions::create([
                BookCopyConditions::COPY_ID => $detail->copy_id,
                BookCopyConditions::USER_ID => $detail->batch->user_id,
                BookCopyConditions::BATCH_ID => $detail->batch_id,
                BookCopyConditions::CONDITION_NOTE => $note,
            ]);
        }
        BookCopy::where('id', $detail->copy_id)->update($updateData);
        Book::where('id', $detail->book_id)->increment('available_copies');
    }

    public function cancelOneBook($id)
    {
        $detail = BookLoansDetail::findOrFail($id)->load('batch');
        $detail->update([
            'borrowed_status' => 'cancel'
        ]);

        if($this->checkAllBatchDetailCanceled($detail->batch->id))
        {
            $detail->batch->update([
                'status' => 'cancel'
            ]);
        }
        Book::where('id', $detail->book_id)->increment('available_copies');
    }

    public function checkAllBatchDetailCanceled($id)
    {
        $batch = $this->getById($id);

        $details = BookLoansDetail::where('batch_id', $id)->where('borrowed_status', 'cancel')->get(); 
        return count($details->toArray()) === count($batch->loanDetails);
    }

    public function getTop6UsersBorrowMost()
    {
        return DB::table('book_loans_batches')
            ->join('users', 'book_loans_batches.user_id', '=', 'users.id')
            ->join('book_loans_details', 'book_loans_batches.id', '=', 'book_loans_details.batch_id')
            ->whereNotIn('book_loans_batches.status', ['pending', 'cancel'])
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(book_loans_details.id) as borrow_count'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('borrow_count')
            ->limit(6)
            ->get();
    }

    public function getBorrowedBooksEachDayLast10Days()
    {
        return DB::table('book_loans_batches')
            ->join('book_loans_details', 'book_loans_batches.id', '=', 'book_loans_details.batch_id')
            ->whereNotIn('book_loans_batches.status', ['pending', 'cancel'])
            ->whereDate('book_loans_batches.borrowed_at', '>=', now()->subDays(9)->toDateString())
            ->whereDate('book_loans_batches.borrowed_at', '<=', now()->toDateString())
            ->select(DB::raw('DATE(book_loans_batches.borrowed_at) as date'),  DB::raw('COUNT(book_loans_details.id) as borrow_count'))
            ->groupBy(DB::raw('DATE(book_loans_batches.borrowed_at)'))
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getReturnedVsReturnedLateRatio()
    {
        $total = DB::table('book_loans_details')
            ->whereIn('borrowed_status', ['returned', 'returned (late)'])
            ->count();

        $returned = DB::table('book_loans_details')
            ->where('borrowed_status', 'returned')
            ->count();

        $returnedLate = DB::table('book_loans_details')
            ->where('borrowed_status', 'returned (late)')
            ->count();

        return [
            'returned' => $returned,
            'returned_late' => $returnedLate,
            'total' => $total,
            'returned_ratio' => $total > 0 ? round($returned / $total, 2) : 0,
            'returned_late_ratio' => $total > 0 ? round($returnedLate / $total, 2) : 0,
        ];
    }
}
