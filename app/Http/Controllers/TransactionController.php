<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Models\User;
use App\Services\TransactionService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends ControllerWithGuard
{
    use APIResponse;
    protected $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        parent::__construct();
        $this->transactionService = $transactionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $size = request()->query('size', 6);
        $q = request()->query('q');
        $user = Auth::user();
        if($user->role === User::ROLE_ADMIN) {
            $transactions = $this->transactionService->getAllTransactions($size, $q);
            return $this->responseSuccessWithData($transactions);
        }

        $transactions = $this->transactionService->getTransactionsOfUser($user->id,$size, $q);
        return $this->responseSuccessWithData($transactions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        $data = $request->only([
            'user_id',
            'batch_id',
            'copy_id',
            'note',
            'amount',
            'type',
        ]);
        $transaction = $this->transactionService->createTransaction($data);
        if(!$transaction) {
            return $this->responseError('Fail to create transaction');
        }

        return $this->responseSuccessWithData($transaction, true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = $this->transactionService->getTransactionById($id);
        if(!$transaction) {
            return $this->responseError('Transaction not found');
        }
        return $this->responseSuccessWithData($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionUpdateRequest $request, $id)
    {
        $data = $request->only([
            'payment_status',
            'payment_method'
        ]);

        $this->transactionService->updateTransaction($id, $data);

        return $this->responseSuccess('Update transaction successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getTransactionsOfLoanBatch($id) {
        $transactions = $this->transactionService->getTransactionsOfLoanBatch($id);
        if(!$transactions) {
            return $this->responseError('Fail to get transactions');
        }
        return $this->responseSuccessWithData($transactions);
    }

    public function getTransactionsOfUser($id) {
        $size = request()->query('size', 6);
        $q = request()->query('q');
        $transactions = $this->transactionService->getTransactionsOfUser($id, $size, $q);
        if(!$transactions) {
            return $this->responseError('Fail to get transactions');
        }
        return $this->responseSuccessWithData($transactions);
    }
}
