<?php

namespace App\Repositories\Book;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookCopyConditions;
use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookRepositoryImplement implements BookRepositoryInterface
{

    public function existsBook($isbn13, $isbn10, $title)
    {
        return Book::when($isbn13, function ($query) use ($isbn13) {
            $query->orWhere('isbn13', $isbn13);
        })
            ->when($isbn10, function ($query) use ($isbn10) {
                $query->orWhere('isbn10', $isbn10);
            })
            ->when($title, function ($query) use ($title) {
                $query->orWhere('title', $title);
            })
            ->exists();
    }
    private function getAllWithTrashed($q  = '')
    {
        return Book::withTrashed()
            ->where('title', 'like', '%' . $q . '%')
            ->with([
                'bookcopies',
                'categories' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn13', 'isbn10', 'language', 'authors', 'num_pages', 'available_copies', 'total_copies', 'thumbnail', 'deleted_at']);
    }

    private function getAllNoTrashed($q = '')
    {
        return Book::with([
            'bookcopies',
            'categories' => function ($query) {
                $query->select('id', 'name');
            }
        ])->where('title', 'like', '%' . $q . '%')->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn13', 'isbn10', 'language', 'authors', 'num_pages', 'available_copies', 'total_copies', 'thumbnail', 'deleted_at']);
    }

    public function getAll($size = 6, $q = '')
    {
        return $this->getAllNoTrashed($q)->paginate($size);
    }


    public function getBooksWithTrashed($size = 10, $q)
    {

        return $this->getAllWithTrashed($q)->paginate($size);
    }

    public function getBooksWithTrashedByCategory($id, $size = 10, $q)
    {
        return $this->getAllWithTrashed($q)
            ->whereHas('categories', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->paginate($size);
    }

    public function getBooksByCategory($id, $size = 10, $q)
    {
        return $this->getAllNoTrashed($q)
            ->whereHas('categories', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->paginate($size);
    }

    public function getById($id)
    {
        return $this->getAllWithTrashed()->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $book = Book::create([
                Book::ID => Str::uuid(),
                Book::TITLE => $data['title'],
                Book::DESCRIPTION => $data['description'] ?? null,
                Book::PUBLICATION_YEAR => $data['publication_year'] ?? null,
                Book::ISBN13 => $data['isbn13'] ?? null,
                Book::ISBN10 => $data['isbn10'] ?? null,
                Book::LANGUAGE => $data['language'] ?? null,
                Book::AUTHORS => $data['authors'] ?? null,
                Book::NUM_PAGES => $data['num_pages'] ?? null,
                Book::AVAILABLE_COPIES => $data['total_copies'] ?? null,
                Book::TOTAL_COPIES => $data['total_copies'] ?? null,
                Book::THUMBNAIL => $data['thumbnail'] ?? null,
            ]);

            for ($i = 1; $i <= $data['total_copies']; $i++) {
                BookCopy::create([
                    BookCopy::ID => Str::uuid(),
                    BookCopy::ACQUIRED_AT => now(),
                    BookCopy::BOOK_ID => $book->id
                ]);
            }

            if (!$book) {
                return null;
            }

            if (isset($data['categories'])) {
                $book->categories()->attach($data['categories'], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return $book;
        });
    }
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $book = Book::findOrFail($id);
            $book->update([
                Book::TITLE => $data['title'],
                Book::DESCRIPTION => $data['description'] ?? null,
                Book::PUBLICATION_YEAR => $data['publication_year'] ?? null,
                Book::ISBN13 => $data['isbn13'] ?? null,
                Book::ISBN10 => $data['isbn10'] ?? null,
                Book::LANGUAGE => $data['language'] ?? null,
                Book::AUTHORS => $data['authors'] ?? null,
                Book::NUM_PAGES => $data['num_pages'] ?? null,
                Book::THUMBNAIL => $data['file'] ?? $data['thumbnail'] ?? null,
            ]);

            if (isset($data['categories'])) {
                $book->categories()->sync($data['categories']);
            }
            return $book;
        });
    }

    public function delete($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }
    public function restore($id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
    }
    public function decreaseAvailableCopies($id)
    {
        $book = Book::findOrFail($id);
        $book->update([
            BOOK::AVAILABLE_COPIES => $book->available_copies - 1
        ]);
    }

    public function increaseAvailableCopies($id)
    {
        $book = Book::findOrFail($id);
        $book->update([
            BOOK::AVAILABLE_COPIES => $book->available_copies + 1
        ]);
    }

    public function getAllBookCopiesOfOneBook($id)
    {
        return BookCopy::where('book_id', '=', $id)->get();
    }


    public function updateBookCopy($id, $status)
    {
        $book = BookCopy::findOrFail($id);
        $book->update([
            BookCopy::STATUS => $status
        ]);
    }

    public function getConditionOfBookCopy($id)
    {
        return BookCopyConditions::where('copy_id', '=', $id)->orderBy('created_at', 'desc')->get();
    }

    public function getTotalBooksByCategory()
    {
        return DB::table('books')
            ->join('book_category', 'books.id', '=', 'book_category.book_id')
            ->join('categories', 'book_category.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name', DB::raw('SUM(books.total_copies) as total_books'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_books', 'desc')
            ->get();
    }

    public function getTotalQuantityBooks()
    {
        return Book::count('id');
    }

    public function getTop6MostBorrowedBooks()
    {
        return Book::select('books.id', 'books.title', DB::raw('COUNT(book_loans_details.id) as borrow_count'))
            ->join('book_loans_details', 'books.id', '=', 'book_loans_details.book_id')
            ->where('book_loans_details.borrowed_status', '!=', 'pending')
            ->where('book_loans_details.borrowed_status', '!=', 'cancel')
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('borrow_count')
            ->limit(6)
            ->get();
    }
}
