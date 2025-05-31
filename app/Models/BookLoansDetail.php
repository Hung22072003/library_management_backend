<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoansDetail extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'note',
        'borrowed_status',
        'returned_condition',
        'book_id',
        'copy_id',
        'batch_id',
        'borrowed_at',
        'due_at',
        'return_at',
        'expired_at',
        'extended_at',
    ];


    /**
     * Get the book that is associated with the loan detail.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookcopies()
    {
        return $this->belongsTo(BookCopy::class);
    }

    /**
     * Get the batch that owns the loan detail.
     */
    public function batch()
    {
        return $this->belongsTo(BookLoansBatch::class, 'batch_id');
    }
}
