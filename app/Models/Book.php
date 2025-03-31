<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;
    const ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const PUBLICATION_YEAR = 'publication_year';
    const ISBN = 'isbn';
    const RENTAL_FEE = 'rental_fee';
    const AVAILABLE_COPIES = 'available_copies';
    const TOTAL_COPIES = 'total_copies';
    const THUMBNAIL = 'thumbnail';

    protected $fillable = [
        self::TITLE,
        self::DESCRIPTION,
        self::PUBLICATION_YEAR,
        self::ISBN,
        self::RENTAL_FEE,
        self::AVAILABLE_COPIES,
        self::TOTAL_COPIES,
        self::THUMBNAIL,
    ];

    protected $casts = [
        self::PUBLICATION_YEAR => 'integer',
        self::RENTAL_FEE => 'decimal:0',
        self::AVAILABLE_COPIES => 'integer',
        self::TOTAL_COPIES => 'integer',
    ];

    // many to many relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id');
    }
}
