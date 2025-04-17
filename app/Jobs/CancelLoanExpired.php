<?php

namespace App\Jobs;

use App\Models\BookLoansBatch;
use App\Services\LoanService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelLoanExpired implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $loan;

    public function __construct(BookLoansBatch $loan)
    {
        $this->loan = $loan;
    }

    public function handle(LoanService $loanService): void
    {
        $loanService->cancelLoan($this->loan);
    }
}
