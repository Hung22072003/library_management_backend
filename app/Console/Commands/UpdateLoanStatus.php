<?php

namespace App\Console\Commands;

use App\Jobs\CancelLoanExpired;
use App\Jobs\UpdateStatusLoanOverdue;
use App\Models\BookLoansBatch;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class UpdateLoanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái của các khoản mượn quá hạn từ pending sang cancel';

    protected $loanService;
    public function __construct(LoanService $loanService)
    {
        parent::__construct();
        $this->loanService = $loanService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredLoans = $this->loanService->getExpiredPendingLoans();
        $overdueLoans = $this->loanService->getOverdueLoans();
        Bus::batch(
            $expiredLoans->map(fn($loan) => new CancelLoanExpired($loan))->all()
        )->dispatch();

        Bus::batch(
            $overdueLoans->map(fn($loan) => new UpdateStatusLoanOverdue($loan))->all()
        )->dispatch();

        $this->info('Update loans dispatched.');
    }
}
