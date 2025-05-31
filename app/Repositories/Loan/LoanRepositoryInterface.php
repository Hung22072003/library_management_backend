<?php

namespace App\Repositories\Loan;

use App\Models\BookLoansBatch;
use App\Repositories\BaseRepositoryInterface;

interface LoanRepositoryInterface extends BaseRepositoryInterface {
    public function getExpiredPendingLoans();
    public function getOverdueLoans();
    public function getNearlyOverdueLoans();
    public function getBatchesOfUser($id, $size = 6);
    public function cancelLoanBatch(BookLoansBatch $batch);
    public function overdueLoanBatch(BookLoansBatch $batch);
    public function returnLoanBatch(BookLoansBatch $batch);
    public function borrowLoanBatch(BookLoansBatch $batch);
    public function extendLoanBatch(BookLoansBatch $loan, $date);
    public function updateReturnDetails(BookLoansBatch $batch, array $returnDetails);
    public function returnOneBook($detail_id, $note, $returnedCondition);
    public function getTop6UsersBorrowMost();
    public function getBorrowedBooksEachDayLast10Days();
    public function getReturnedVsReturnedLateRatio();
}
