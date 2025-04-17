<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoansDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'note',
        'returned_at',
        'borrowed_status',
        'returned_condition',
        'rental_fee',
        'late_fee_per_day',
        'book_id',
        'batch_id',
    ];

    protected $dates = [
        'returned_at',
    ];

    /**
     * Get the book that is associated with the loan detail.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the batch that owns the loan detail.
     */
    public function batch()
    {
        return $this->belongsTo(BookLoansBatch::class, 'batch_id');
    }
}
