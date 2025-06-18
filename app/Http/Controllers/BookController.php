<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\BookService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Type\Integer;

class BookController extends ControllerWithGuard
{
    use APIResponse;
    private $bookService;
    public function __construct(BookService $bookService)
    {
        parent::__construct();
        $this->bookService = $bookService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $size = request()->query('size', 6);
        $q = request()->query('q');
        $user = Auth::user();
        if($user->role === User::ROLE_ADMIN) {
            $books = $this->bookService->getBooksWithTrashed($size, $q);
            return $this->responseSuccessWithData($books);
        }
        $books = $this->bookService->getAllBooks($size, $q);
        return $this->responseSuccessWithData($books);
    }

    public function getAllBookCopiesOfOneBook($id) {
        return $this->responseSuccessWithData($this->bookService->getAllBookCopiesOfOneBook($id));
    }
    public function getBooksByCategory($id)
    {
        $size = request()->query('size', 10);
        $q = request()->query('q');
        $user = Auth::user();
        if($user->role === User::ROLE_ADMIN) {
            $books = $this->bookService->getBooksWithTrashedByCategory($id, $size, $q);
            return $this->responseSuccessWithData($books);
        }
        $books = $this->bookService->getBooksByCategory($id, $size, $q);
        return $this->responseSuccessWithData($books);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookStoreRequest $request)
    {
        Gate::authorize('admin');
        $data = $request->only([
            'title',
            'description',
            'publication_year',
            'isbn13',
            'isbn10',
            'language',
            'authors',
            'num_pages',
            'total_copies',
            'thumbnail',
            'categories'
        ]);
        
        $book = $this->bookService->createBook($data);
        if (!$book) {
            return $this->responseError('Failed to create book', 500);
        }
        return $this->responseSuccessWithData($book, true);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        $book = $this->bookService->getBookById($id);
        if (!$book) {
            return $this->responseError('Book not found');
        }
        return $this->responseSuccessWithData($book);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookUpdateRequest $request, string $id) {
        Gate::authorize('admin');
        $data = $request->only([
            'title',
            'description',
            'publication_year',
            'isbn13',
            'isbn10',
            'language',
            'authors',
            'num_pages',
            'thumbnail',
            'file',
            'categories',
        ]);

        $book = $this->bookService->updateBook($id, $data);
        if (!$book) {
            return $this->responseError('Failed to update book');
        }
        return $this->responseSuccess('Updated book successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('admin');
        $this->bookService->deleteBook($id);
        return $this->responseSuccess('Book deleted successfully');
    }

    public function restore($id)
    {
        Gate::authorize('admin');
        $this->bookService->restoreBook($id);
        return $this->responseSuccess('Book restored successfully');
    }

    public function import(Request $request)
    {
        Gate::authorize('admin');
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $result = $this->bookService->importBooksFromExcel($request->file('file'));

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['status']);
    }

    public function addBookCopies($id, Request $request) {
        $request->validate([
            'num' => 'required|integer',
        ]);
        $this->bookService->addBookCopies($id, $request->get('num'));
        return $this->responseSuccess();
    }
}
