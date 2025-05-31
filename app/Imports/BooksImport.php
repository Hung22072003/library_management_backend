<?php

namespace App\Imports;

use App\Repositories\Book\BookRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BooksImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }

    public function model(array $row)
    {
        $isbn10 = $row['isbn10'] ?? null;
        if ($isbn10 !== null) {
            $isbn10 = str_pad($isbn10, 10, '0', STR_PAD_LEFT);
        }
        if ($this->bookRepository->existsBook($row['isbn13'], $isbn10, $row['title'])) {
            Log::info('Book already exists', [
                'isbn13' => $row['isbn13'],
                'isbn10' => $isbn10,
                'title' => $row['title']
            ]);
            return null;
        }

        $categories = [];
        if (!empty($row['categories'])) {
            $categories = array_map('trim', explode(',', $row['categories']));
        }
        return $this->bookRepository->create([
            'title' => $row['title'],
            'authors' => $row['authors'],
            'isbn10' => $isbn10,
            'isbn13' => $row['isbn13'] ?? null,
            'language' => $row['language'] ?? null,
            'num_pages' => $row['num_pages'] ?? null,
            'thumbnail' => $row['thumbnail'] ?? null,
            'total_copies' => $row['total_copies'] ?? 1,
            'description' => $row['description'] ?? null,
            'publication_year' => $row['published_year'] ?? null,
            'categories' => $categories,
        ]);
    }
}
