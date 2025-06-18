<?php

namespace App\Services;

use App\Imports\BooksImport;
use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
        if (isset($data['thumbnail'])) {
            $path = $data['thumbnail']->store('images', 's3');
            $url =$this->baseUrl . $path;
            $data['thumbnail'] = $url;
        }
        return $this->bookRepository->create($data);
    }

    public function importBooksFromExcel($file)
    {
        try {
            Log::info('Book import started', ['file' => $file->getClientOriginalName()]);
            Excel::import(new BooksImport($this->bookRepository), $file);
            return [
                'status' => 200,
                'message' => 'Books imported successfully!',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Error importing books: ' . $e->getMessage(),
            ];
        }
    }

    public function updateBook($id, array $data)
    {
        $book = $this->getBookById($id);
        if (!$book) {
            return null;
        }
        //upload to aws s3
        if (isset($data['file'])) {
            $path = $data['file']->store('images', 's3');
            $url = $this->baseUrl. $path;
            $data['file'] = $url;
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

    public function getAllBookCopiesOfOneBook($id) {
        return $this->bookRepository->getAllBookCopiesOfOneBook($id);
    }

    public function updateBookCopy($id, $status) {
        return $this->bookRepository->updateBookCopy($id, $status);
    }

    public function getConditionOfBookCopy($id) {
        return $this->bookRepository->getConditionOfBookCopy($id);
    }

    public function addBookCopies($id, $num) {
        $this->bookRepository->addBookCopies($id, $num);
    }
}
