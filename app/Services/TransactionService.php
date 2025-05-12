<?php

namespace App\Services;

use App\Repositories\Transaction\TransactionRepositoryInterface;

class TransactionService
{
    protected $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function getAllTransactions($size, $q) {
        return $this->transactionRepository->getAll($size, $q);
    }

    public function getTransactionsOfUser($id, $size, $q) {
        return $this->transactionRepository->getTransactionsOfUser($id, $size, $q);
    }
    public function getTransactionById($id) {
        return $this->transactionRepository->getById($id);
    }
    public function createTransaction(array $data)
    {
        return $this->transactionRepository->create($data);
    }

    public function updateTransaction($id, array $data)
    {
        return $this->transactionRepository->update($id, $data);
    }
    public function getTransactionsOfLoanBatch($id) {
        return $this->transactionRepository->getTransactionsOfLoanBatch($id);
    }

}
