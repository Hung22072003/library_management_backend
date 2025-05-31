<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyOverduePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $transaction;
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle(NotificationService $notificationService): void
    {
        $this->transaction->refresh();
        $notificationService->createNotification([
            'type' => 'overdue_payment',
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->user_id,
        ]);
    }
}
