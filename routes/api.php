<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Users
Route::resource('users', UserController::class);

// Books
Route::resource('books', BookController::class);
Route::get('books/category/{id}', [BookController::class, 'getBooksByCategory']);
Route::post('books/{id}', [BookController::class, 'restore']);

//Profile
Route::get('profile/me', [UserController::class, 'me']);

//Category
Route::resource('categories', CategoryController::class);

//Author
Route::resource('authors', AuthorController::class);