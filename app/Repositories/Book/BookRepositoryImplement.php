<?php

namespace App\Repositories\Book;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookCopyConditions;
use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ])->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn13', 'isbn10', 'language', 'authors', 'num_pages', 'available_copies', 'total_copies', 'thumbnail', 'floor', 'shelf', 'row', 'col', 'deleted_at']);
    }

    private function getAllNoTrashed($q = '')
    {
        return Book::with([
            'bookcopies',
            'categories' => function ($query) {
                $query->select('id', 'name');
            }
        ])->where('title', 'like', '%' . $q . '%')->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn13', 'isbn10', 'language', 'authors', 'num_pages', 'available_copies', 'total_copies', 'thumbnail', 'floor', 'shelf', 'row', 'col', 'deleted_at']);
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
    function generateUniqueBookId(): string
    {
        do {
            $id = 'DUT' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Book::where(Book::ID, $id)->exists());

        return $id;
    }
     public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $nextNumber = $this->getNextBookNumber();
            $bookId = 'DUT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $categoryId = $data['categories'][0] ?? '000';
            $existingCount = Book::withTrashed()->with('categories')->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('id', $categoryId);
            })->count();
            $location = $this->getBookLocation($existingCount, $categoryId);

            $book = Book::create([
                'id' => $bookId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'publication_year' => $data['publication_year'] ?? null,
                'isbn13' => $data['isbn13'] ?? null,
                'isbn10' => $data['isbn10'] ?? null,
                'language' => $data['language'] ?? null,
                'authors' => $data['authors'] ?? null,
                'num_pages' => $data['num_pages'] ?? null,
                'available_copies' => $data['total_copies'] ?? 0,
                'total_copies' => $data['total_copies'] ?? 0,
                'thumbnail' => $data['thumbnail'] ?? null,
                'floor' => $location['floor'],
                'shelf' => $location['shelf'],
                'row' => $location['row'],
                'col' => $location['col'],
            ]);

            for ($i = 1; $i <= ($data['total_copies'] ?? 0); $i++) {
                BookCopy::create([
                    'id' => $bookId . '-' . $i,
                    'acquired_at' => now(),
                    'book_id' => $bookId,
                ]);
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

    private function getNextBookNumber(): int
    {
        $latestBook = Book::orderByDesc('id')->first();

        if ($latestBook && preg_match('/^DUT(\d{6})$/', $latestBook->id, $matches)) {
            return (int)$matches[1] + 1;
        }

        return 1;
    }

    private function getBookLocation(int $indexInCategory, int $categoryId): array
    {
        // Mỗi shelf chứa tối đa 100 sách (10 row x 10 col)
        $shelfIndex = intdiv($indexInCategory, 100); // 0 = A, 1 = B, 2 = C, ...
        $shelfLetter = chr(ord('A') + $shelfIndex);

        $positionInShelf = $indexInCategory % 100;
        $row = intdiv($positionInShelf, 10) + 1;
        $col = ($positionInShelf % 10) + 1;

        return [
            'floor' => '2',
            'shelf' => $shelfLetter . str_pad($categoryId, 3, '0', STR_PAD_LEFT),
            'row' => (string) $row,
            'col' => (string) $col,
        ];
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
