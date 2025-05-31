<?php

namespace App\Jobs;

use App\Models\BookLoansBatch;
use App\Services\NotificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyNearlyOverdueLoan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $loan;
    /**
     * Create a new job instance.
     */
    public function __construct(BookLoansBatch $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $this->loan->refresh();
        $notificationService->createNotification([
            'type' => 'overdue_loan',
            'batch_id' => $this->loan->id,
            'user_id' => $this->loan->user_id,
        ]);
    }
}
