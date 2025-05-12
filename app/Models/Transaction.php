<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;
    const NOTE = 'note';
    const AMOUNT = 'amount';
    const TYPE = 'type';
    const PAYMENT_STATUS = 'payment_status';
    const PAYMENT_METHOD = 'payment_method';
    const PAYMENT_EXPIRED_AT = 'payment_expired_at';
    const BATCH_ID = 'batch_id';
    const USER_ID = 'user_id';
    const COPY_ID = 'copy_id';

    protected $fillable = [
        self::NOTE,
        self::AMOUNT,
        self::TYPE,
        self::PAYMENT_STATUS,
        self::PAYMENT_METHOD,
        self::PAYMENT_EXPIRED_AT,
        self::BATCH_ID,
        self::USER_ID,
        self::COPY_ID,
    ];

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookLoansBatch()
    {
        return $this->belongsTo(BookLoansBatch::class);
    }
}
