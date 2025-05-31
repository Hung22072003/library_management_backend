<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookLoansBatch extends Model
{
    use HasFactory, HasUuids;
    use SoftDeletes;

    const BORROWED_AT = 'borrowed_at';
    const DUE_AT = 'due_at';
    const RETURN_AT = 'return_at';
    const STATUS = 'status';
    const EXPIRED_AT = 'expired_at';
    const EXTENDED_AT = 'extended_at';
    const USER_ID = 'user_id';

    protected $fillable = [
        self::BORROWED_AT,
        self::DUE_AT,
        self::RETURN_AT,
        self::STATUS,
        self::EXPIRED_AT,
        self::EXTENDED_AT,
        self::USER_ID,
    ];

    protected $dates = [
        self::BORROWED_AT,
        self::DUE_AT,
        self::RETURN_AT,
        self::EXPIRED_AT,
        self::EXTENDED_AT,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loan details for the book loan batch.
     */
    public function loanDetails()
    {
        return $this->hasMany(BookLoansDetail::class, 'batch_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'batch_id');
    }
}
