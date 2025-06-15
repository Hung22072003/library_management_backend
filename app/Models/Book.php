<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    const ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const PUBLICATION_YEAR = 'publication_year';
    const ISBN13 = 'isbn13';
    const ISBN10 = 'isbn10';
    const LANGUAGE = 'language';
    const AUTHORS = 'authors';
    const NUM_PAGES = 'num_pages';
    const AVAILABLE_COPIES = 'available_copies';
    const TOTAL_COPIES = 'total_copies';
    const THUMBNAIL = 'thumbnail';
    const FLOOR = 'floor';
    const SHELF = 'shelf';
    const ROW = 'row';
    const COL = 'col';

    protected $fillable = [
        self::ID,
        self::TITLE,
        self::DESCRIPTION,
        self::PUBLICATION_YEAR,
        self::ISBN13,
        self::ISBN10,
        self::LANGUAGE,
        self::AUTHORS,
        self::NUM_PAGES,
        self::AVAILABLE_COPIES,
        self::TOTAL_COPIES,
        self::THUMBNAIL,
        self::FLOOR,
        self::SHELF,
        self::ROW,
        self::COL,
    ];

    protected $casts = [
        self::PUBLICATION_YEAR => 'integer',
        self::AVAILABLE_COPIES => 'integer',
        self::TOTAL_COPIES => 'integer',
        self::NUM_PAGES => 'integer',
    ];

    public function bookcopies()
    {
        return $this->hasMany(BookCopy::class, 'book_id');
    }
    // many to many relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }
}
