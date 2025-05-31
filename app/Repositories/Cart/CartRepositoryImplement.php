<?php

namespace App\Repositories\Cart;

use App\Models\Book;
use App\Models\Cart;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class CartRepositoryImplement implements CartRepositoryInterface
{
    public function getAll($size = 6, $q = '') {}
    public function getById($id)
    {
        return Cart::find($id);
    }
    public function create(array $data)
    {
        return Cart::create([
            Cart::ID => Str::uuid(),
            Cart::BOOK_ID => $data['book_id'],
            Cart::COPY_ID => $data['copy_id'],
            Cart::USER_ID => $data['user_id']
        ]);
    }
    public function update($id, array $data) {}
    public function delete($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();
    }

    public function existsCart($book_id, $user_id)
    {
        return Cart::where('book_id', '=', $book_id)->where('user_id', '=', $user_id)->first();
    }

    public function getCartsOfUser($user_id)
    {
        return Cart::where('user_id', '=', $user_id)->with('book.categories')->orderBy('id', 'desc')->get();
    }

    public function clearCarts($user_id)
    {
        Cart::where('user_id', '=', $user_id)->delete();
    }
}
