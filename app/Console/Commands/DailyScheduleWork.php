<?php

namespace App\Console\Commands;

use App\Jobs\CancelLoanExpired;
use App\Jobs\NotifyNearlyOverdueLoan;
use App\Jobs\NotifyOverduePayment;
use App\Jobs\UpdateStatusLoanOverdue;
use App\Services\LoanService;
use App\Services\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class DailyScheduleWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'works:update-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái của các khoản mượn và thông báo giao dịch quá hạn';

    protected $loanService;
    protected $transactionService;
    public function __construct(LoanService $loanService, TransactionService $transactionService)
    {
        parent::__construct();
        $this->loanService = $loanService;
        $this->transactionService = $transactionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredLoans = $this->loanService->getExpiredPendingLoans();
        $overdueLoans = $this->loanService->getOverdueLoans();
        $nearlyOverdueLoans = $this->loanService->getNearlyOverdueLoans();
        Log::info('Nearly overdue loans: ', $nearlyOverdueLoans->toArray());
        $transactions = $this->transactionService->getOverdueTransactions();
        Bus::batch(
            $expiredLoans->map(fn($loan) => new CancelLoanExpired($loan))->all()
        )->dispatch();


        Bus::batch(
            $nearlyOverdueLoans->map(fn($loan) => new NotifyNearlyOverdueLoan($loan))->all()
        )->dispatch();

        Bus::batch(
            $overdueLoans->map(fn($loan) => new UpdateStatusLoanOverdue($loan))->all()
        )->dispatch();

        Bus::batch(
            $transactions->map(fn($transaction) => new NotifyOverduePayment($transaction))->all()
        )->dispatch();

    }
}
