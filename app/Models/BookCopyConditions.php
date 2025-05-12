<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopyConditions extends Model
{
    use HasFactory, HasUuids;

    const ID = 'id';
    const COPY_ID = 'copy_id';
    const USER_ID = 'user_id';
    const BATCH_ID = 'batch_id';
    const CONDITION_NOTE = 'condition_note';

    protected $fillable = [
        self::ID,
        self::COPY_ID,
        self::USER_ID,
        self::BATCH_ID,
        self::CONDITION_NOTE,
    ];
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class, self::COPY_ID);
    }
    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }
    public function batch()
    {
        return $this->belongsTo(BookLoansBatch::class, self::BATCH_ID);
    }
}
