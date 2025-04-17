<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory;
    const RENTAL_FEE = 'rental_fee';
    const BOOK_ID = 'book_id';
    const USER_ID = 'user_id';

    protected $fillable = [
        self::RENTAL_FEE,
        self::BOOK_ID,
        self::USER_ID,
    ];

    protected $casts = [
        self::RENTAL_FEE => 'decimal:0',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
