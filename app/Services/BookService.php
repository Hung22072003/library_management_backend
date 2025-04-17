<?php

namespace App\Services;

use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class BookService
{
    protected $baseUrl;
    private $bookRepository;
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->baseUrl = config('filesystems.disks.s3.aws_url');
    }

    public function getAllBooks($size, $q)
    {
        return $this->bookRepository->getAll($size, $q);
    }

    public function getBooksWithTrashed($size, $q)
    {
        return $this->bookRepository->getBooksWithTrashed($size, $q);
    }
    public function getBooksWithTrashedByCategory($id, $size, $q)
    {
        return $this->bookRepository->getBooksWithTrashedByCategory($id, $size, $q);
    }

    public function getBooksByCategory($id, $size, $q)
    {
        return $this->bookRepository->getBooksByCategory($id, $size, $q);
    }

    public function getBookById($id)
    {
        return $this->bookRepository->getById($id);
    }

    public function createBook(array $data)
    {
        //upload to aws s3
        if (isset($data['thumbnail'])) {
            $path = $data['thumbnail']->store('images', 's3');
            $url =$this->baseUrl . $path;
            $data['thumbnail'] = $url;
        }
        return $this->bookRepository->create($data);
    }

    public function updateBook($id, array $data)
    {
        $book = $this->getBookById($id);
        if (!$book) {
            return null;
        }
        //upload to aws s3
        if (isset($data['thumbnail'])) {
            $path = $data['thumbnail']->store('images', 's3');
            $url = $this->baseUrl. $path;
            $data['thumbnail'] = $url;
            if($book->thumbnail) {
                Storage::disk('s3')->delete(str_replace($this->baseUrl, "", $book->thumbnail));
            }
        }
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

    public function decreaseAvailableCopies($id) {
        return $this->bookRepository->decreaseAvailableCopies($id);
    }

    public function increaseAvailableCopies($id) {
        return $this->bookRepository->increaseAvailableCopies($id);
    }
}
