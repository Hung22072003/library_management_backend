<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory, HasUuids;
    const ID = 'id';
    const BOOK_ID = 'book_id';
    const USER_ID = 'user_id';
    const COPY_ID = 'copy_id';
    protected $fillable = [
        self::ID,
        self::BOOK_ID,
        self::USER_ID,
        self::COPY_ID,
    ];

    public function book()
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }

    public function bookcopies()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
