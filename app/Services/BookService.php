<?php

namespace App\Services;

use App\Repositories\Book\BookRepositoryInterface;

class BookService
{
    private $bookRepository;
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function getAllBooks($size)
    {
        return $this->bookRepository->getAll($size);
    }

    public function getBooksByCategory($id, $size)
    {
        return $this->bookRepository->getBooksByCategory($id, $size);
    }

    public function getBookById($id)
    {
        return $this->bookRepository->getById($id);
    }

    public function createBook(array $data)
    {
        return $this->bookRepository->create($data);
    }

    public function updateBook($id, array $data)
    {
        return $this->bookRepository->update($id, $data);
    }
    public function deleteBook($id)
    {
        return $this->bookRepository->delete($id);
    }

    public function restoreBook($id)
    {
        return $this->bookRepository->restore($id);
    }
}
