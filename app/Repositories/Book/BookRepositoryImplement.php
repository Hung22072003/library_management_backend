<?php

namespace App\Repositories\Book;

use App\Models\Book;
use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BookRepositoryImplement implements BookRepositoryInterface
{

    private function getAllWithTrashed($q  = '')
    {
        return Book::withTrashed()
        ->where('title', 'like', '%'.$q.'%')
        ->with([
            'categories' => function ($query) {
                $query->select('id', 'name');
            },
            'authors' => function ($query) {
                $query->select('id', 'name', 'biography');
            }
        ])->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn', 'rental_fee', 'available_copies', 'total_copies', 'thumbnail', 'deleted_at']);
    }

    private function getAllNoTrashed($q = '')
    {
        return Book::with([
            'categories' => function ($query) {
                $query->select('id', 'name');
            },
            'authors' => function ($query) {
                $query->select('id', 'name', 'biography');
            }
        ])
        ->where('title', 'like', '%'.$q.'%')->orderBy('id', 'desc')->select(['id', 'title', 'description', 'publication_year', 'isbn', 'rental_fee', 'available_copies', 'total_copies', 'thumbnail', 'deleted_at']);
    }

    public function getAll($size = 10, $q)
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
                Book::TITLE => $data['title'],
                Book::DESCRIPTION => $data['description'],
                Book::PUBLICATION_YEAR => $data['publication_year'],
                Book::ISBN => $data['isbn'],
                Book::RENTAL_FEE => $data['rental_fee'],
                Book::AVAILABLE_COPIES => $data['available_copies'],
                Book::TOTAL_COPIES => $data['total_copies'],
                Book::THUMBNAIL => $data['thumbnail'] ?? null,
            ]);

            if (!$book) {
                return null;
            }

            if (isset($data['categories'])) {
                $book->categories()->attach($data['categories'], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (isset($data['authors'])) {
                $book->authors()->attach($data['authors'], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return $book;
        });
    }
    public function update($id, array $data) {
        return DB::transaction(function () use ($id, $data) {
            $book = Book::findOrFail($id);
            $book->update([
                Book::TITLE => $data['title'],
                Book::DESCRIPTION => $data['description'],
                Book::PUBLICATION_YEAR => $data['publication_year'],
                Book::ISBN => $data['isbn'],
                Book::RENTAL_FEE => $data['rental_fee'],
                Book::TOTAL_COPIES => $data['total_copies'],
                Book::THUMBNAIL => $data['thumbnail'] ?? $book->thumbnail,
            ]);

            if (isset($data['categories'])) {
                $book->categories()->sync($data['categories']);
            }

            if (isset($data['authors'])) {
                $book->authors()->sync($data['authors']);
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
}
