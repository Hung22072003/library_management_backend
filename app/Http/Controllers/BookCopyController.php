<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class BookCopyController extends ControllerWithGuard
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
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conditions = $this->bookService->getConditionOfBookCopy($id);
        return $this->responseSuccessWithData($conditions);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
