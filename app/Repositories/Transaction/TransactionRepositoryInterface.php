<?php

namespace App\Repositories\Transaction;

use App\Repositories\BaseRepositoryInterface;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getTransactionsOfLoanBatch($id);
    public function getTransactionsOfUser($id, $size = 6, $q = '');
    public function getOverdueTransactions();
    public function getTotalAmountSuccess();
}
