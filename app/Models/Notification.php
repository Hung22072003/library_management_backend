<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, HasUuids;
    use SoftDeletes;
    const ID = 'id';
    const USER_ID = 'user_id';
    const MESSAGE = 'message';
    const TYPE = 'type';
    const IS_READ = 'is_read';
    const BATCH_ID = 'batch_id';
    const TRANSACTION_ID = 'transaction_id';

    protected $fillable = [
        self::ID,
        self::USER_ID,
        self::MESSAGE,
        self::TYPE,
        self::IS_READ,
        self::BATCH_ID,
        self::TRANSACTION_ID,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }

    public function batch()
    {
        return $this->belongsTo(BookLoansBatch::class, self::BATCH_ID);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, self::TRANSACTION_ID);
    }
}
