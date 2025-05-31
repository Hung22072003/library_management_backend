<?php

namespace App\Repositories\Book;

use App\Repositories\BaseRepositoryInterface;

interface BookRepositoryInterface extends BaseRepositoryInterface
{
    public function getBooksWithTrashed($size = 10, $q);
    public function getBooksWithTrashedByCategory($id, $size, $q);
    public function getBooksByCategory($id, $size, $q);
    public function restore($id);
    public function decreaseAvailableCopies($id);
    public function increaseAvailableCopies($id);
    public function getAllBookCopiesOfOneBook($id);
    public function updateBookCopy($id, $status);
    public function getConditionOfBookCopy($id);
    public function getTotalQuantityBooks();
    public function getTotalBooksByCategory();
    public function getTop6MostBorrowedBooks();
    public function existsBook($isbn13, $isbn10, $title);
}
