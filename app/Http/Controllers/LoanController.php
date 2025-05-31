<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookReturnRequest;
use App\Http\Requests\LoanStoreRequest;
use App\Services\LoanService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class LoanController extends ControllerWithGuard
{
    use APIResponse;
    private $loanService;
    public function __construct(LoanService $loanService)
    {
        parent::__construct();
        $this->loanService = $loanService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $size = request()->query('size', 6);
        $q = request()->query('q');
        $batches = $this->loanService->getAllBatches($size, $q);
        return $this->responseSuccessWithData($batches);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $batches = $this->loanService->getOverdueLoans();
        return $this->responseSuccessWithData($batches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LoanStoreRequest $request)
    {
        $user = Auth::user();
        $data = $request->only([
            'borrowed_at',
            'due_date'
        ]);
        $data['user_id'] = $user->id;
        $result = $this->loanService->createLoanBatch($data);
        if ($result['status'] == 201) {
            return $this->responseSuccess($result['message'], $result['status']);
        }
        return $this->responseError($result['message'], $result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = $this->loanService->getBatchById($id);
        if (!$batch) {
            return $this->responseError('Batch not found');
        }
        return $this->responseSuccessWithData($batch);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateStatusBatch(Request $request, string $id)
    {
        $request->validate([
            "status" => "required|string|in:cancel,borrowed"
        ]);
        $status = $request->get('status');
        $batch = $this->loanService->getBatchById($id);
        if (!$batch) {
            return $this->responseError('Batch not found');
        }
        $this->loanService->updateStatusBatch($batch, $status);
        return $this->responseSuccess("Update status batch successfully");
    }

    public function extendLoanBatch(Request $request, string $id)
    {
        $request->validate([
            "date" => "required|date_format:Y-m-d"
        ]);
        $batch = $this->loanService->getBatchById($id);
        if (!$batch) {
            return $this->responseError('Batch not found');
        }
        $this->loanService->extendLoanBatch($batch, $request->get('date'));
        return $this->responseSuccess("Extend batch successfully");
    }

    public function returnMultipleBooks(BookReturnRequest $request)
    {
        Gate::authorize('admin');
        $data = $request->only([
            'loan_batch_id',
            'returns'
        ]);

        Log::info($data);

        $this->loanService->processMultipleReturns($data['loan_batch_id'], $data['returns'] ?? []);

        return $this->responseSuccess('Return books successfully');
    }

    public function returnOneBook(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'detail_id' => 'required|string',
            'note' => 'required|string',
            'returned_condition' => 'required|string|in:good,damaged,lost'
        ]);
        $this->loanService->returnOneBook(
            $request->get('detail_id'),
            $request->get('note'),
            $request->get('returned_condition')
        );
        return $this->responseSuccess("Return book successfully");
    }

    public function getBatchesOfUser() {
        $user = Auth::user();
        $size = request()->query('size', 6);
        $batches = $this->loanService->getBatchesOfUser($user->id, $size);
        return $this->responseSuccessWithData($batches);
    }
}
