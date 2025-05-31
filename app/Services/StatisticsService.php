<?php

namespace App\Services;

use App\Repositories\Book\BookRepositoryInterface;
use App\Repositories\Loan\LoanRepositoryInterface;
use App\Repositories\Transaction\TransactionRepositoryInterface;

class StatisticsService
{
    public $bookRepository;
    public $loanRepository;
    public $transactionRepository;
    public function __construct(BookRepositoryInterface $bookRepository, LoanRepositoryInterface $loanRepository, TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->bookRepository = $bookRepository;
        $this->loanRepository = $loanRepository;
    }

    public function getTotalBooksByCategory()
    {
        return $this->bookRepository->getTotalBooksByCategory();
    }

    public function getTotalQuantityBooks()
    {
        return $this->bookRepository->getTotalQuantityBooks();
    }

    public function getTop6MostBorrowedBooks()
    {
        return $this->bookRepository->getTop6MostBorrowedBooks();
    }

    public function getTop6UsersBorrowMost()
    {
        return $this->loanRepository->getTop6UsersBorrowMost();
    }

    public function getBorrowedBooksEachDayLast10Days()
    {
        return $this->loanRepository->getBorrowedBooksEachDayLast10Days();
    }

    public function getReturnedVsReturnedLateRatio()
    {
        return $this->loanRepository->getReturnedVsReturnedLateRatio();
    }

    public function getTotalAmountSuccess()
    {
        return $this->transactionRepository->getTotalAmountSuccess();
    }
}
