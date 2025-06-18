<?php

namespace App\Services;

use App\Models\BookLoansBatch;
use App\Repositories\Loan\LoanRepositoryInterface;
use Illuminate\Support\Facades\Log;

class LoanService
{
    private $loanRepository;
    private $cartService;
    private $bookService;
    private $notificationService;
    public function __construct(LoanRepositoryInterface $loanRepository, CartService $cartService, BookService $bookService, NotificationService $notificationService)
    {
        $this->loanRepository = $loanRepository;
        $this->cartService = $cartService;
        $this->bookService = $bookService;
        $this->notificationService = $notificationService;
    }
    public function getAllBatches($size, $q)
    {
        return $this->loanRepository->getAll($size, $q);
    }
    public function getBatchesOfUser($id, $size = 6)
    {
        return $this->loanRepository->getBatchesOfUser($id, $size);
    }
    public function getBatchById(string $id)
    {
        return $this->loanRepository->getById($id);
    }

    public function createLoanBatch($data)
    {
        $carts = $this->cartService->getCartsOfUser($data['user_id']);
        if (count($carts) === 0) return [
            'message' => 'Carts is empty',
            'status' => 400,
        ];;

        $data['carts'] = $carts;
        $loanBatch = $this->loanRepository->create($data);
        if ($loanBatch) {
            $this->cartService->clearCarts($data['user_id']);
            foreach ($carts as $cart) {
                $this->bookService->updateBookCopy($cart->copy_id, "borrowed");
            }

            $this->notificationService->createNotification([
                'type' => 'borrow_loan',
                'batch_id' => $loanBatch->id,
            ]);

            return [
                'message' => 'Create Loan Batch successfully',
                'status' => 201,
            ];
        }

        return [
            'message' => 'Failed to create loan batch',
            'status' => 400,
        ];
    }

    public function getExpiredPendingLoans()
    {
        return $this->loanRepository->getExpiredPendingLoans();
    }

    public function getOverdueLoans()
    {
        return $this->loanRepository->getOverdueLoans();
    }

    public function getNearlyOverdueLoans()
    {
        return $this->loanRepository->getNearlyOverdueLoans();
    }
    public function cancelLoan(BookLoansBatch $loan)
    {
        $this->loanRepository->cancelLoanBatch($loan);
        // $this->notificationService->createNotification([
        //     'type' => 'cancel_loan',
        //     'batch_id' => $loan->id,
        //     'user_id' => $loan->user_id,
        // ]);
    }

    public function overdueLoan(BookLoansBatch $loan)
    {
        $this->loanRepository->overdueLoanBatch($loan);
        // $this->notificationService->createNotification([
        //     'type' => 'overdue_loan',
        //     'batch_id' => $loan->id,
        //     'user_id' => $loan->user_id,
        // ]);
    }

    public function extendLoanBatch(BookLoansBatch $loan, $date)
    {
        $this->loanRepository->extendLoanBatch($loan, $date);
    }
    public function updateStatusBatch($batch, $status)
    {
        switch ($status) {
            case 'cancel': {
                    $this->cancelLoan($batch);
                    foreach ($batch->loanDetails as $detail) {
                        $this->bookService->increaseAvailableCopies($detail->book_id);
                        $this->bookService->updateBookCopy($detail->copy_id, "available");
                    }
                    break;
                }
            case 'borrowed': {
                    $this->loanRepository->borrowLoanBatch($batch);
                    break;
                }
        }
    }

    public function processMultipleReturns(string $loanBatchId, array $returnDetails)
    {
        $batch = $this->getBatchById($loanBatchId);
        $this->loanRepository->updateReturnDetails($batch, $returnDetails);
        if (!empty($returnDetails)) {
            foreach ($returnDetails as $detail) {
                $this->bookService->increaseAvailableCopies($detail['book_id']);
            }
        }
    }

    public function returnOneBook($detail_id, $note, $returnedCondition)
    {
        $this->loanRepository->returnOneBook($detail_id, $note, $returnedCondition);
    }

    public function cancelOneBook($id)
    {
        $this->loanRepository->cancelOneBook($id);
    }
}
