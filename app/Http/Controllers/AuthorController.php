<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    use APIResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = Author::all();
        return $this->responseSuccessWithData($authors);
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
        //
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
