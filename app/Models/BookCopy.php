<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCopy extends Model
{
    use HasFactory, HasUuids;
    use SoftDeletes;

    const ID = 'id';
    const STATUS = 'status';
    const CONDITION = 'condition';
    const ACQUIRED_AT = 'acquired_at';
    const BOOK_ID = 'book_id';

    protected $fillable = [
        self::ID,
        self::STATUS,
        self::CONDITION,
        self::ACQUIRED_AT,
        self::BOOK_ID,
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function conditions()
    {
        return $this->hasMany(BookCopyConditions::class, self::ID);
    }
}
