<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    const ID = 'id';
    const NAME = 'name';
    protected $fillable = [
        self::ID,
        self::NAME,
    ];
    protected $casts = [
        self::ID => 'string',
        self::NAME => 'string',
    ];
    // many to many relationship with books
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category', 'category_id', 'book_id');
    }
    // many to many relationship with books
    public function booksWithPivot()
    {
        return $this->belongsToMany(Book::class, 'book_category', 'category_id', 'book_id')->withPivot('created_at', 'updated_at');
    }
}
