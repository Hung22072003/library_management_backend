<?php

namespace App\Services;

use App\Repositories\Cart\CartRepositoryInterface;

class CartService
{
    private $cartRepository;
    private $bookService;
    public function __construct(CartRepositoryInterface $cartRepository, BookService $bookService)
    {
        $this->cartRepository = $cartRepository;
        $this->bookService = $bookService;
    }

    public function getCartsOfUser($userId)
    {
        return $this->cartRepository->getCartsOfUser($userId);
    }

    public function createCart($data)
    {
        $existCarts = $this->getCartsOfUser($data['user_id']);
        if(count($existCarts) === 3) {
            return [
                'message' => 'Maximum 3 books in cart',
                'status' => 400
            ];
        }

        $book = $this->bookService->getBookById($data['book_id']);
        if ($book->available_copies === 0) {
            return [
                'message' => 'Available copies are out of stock',
                'status' => 400
            ];
        }

        $cart = $this->existsCart($data['book_id'], $data['user_id']);
        if ($cart) {
            return [
                'message' => 'Book is already exists in cart',
                'status' => 400
            ];
        }
        
        $result = $this->cartRepository->create($data);

        if ($result) {
            $this->bookService->decreaseAvailableCopies($data['book_id']);
            $this->bookService->updateBookCopy($data['copy_id'], "in_cart");
            return [
                'message' => 'Create cart successfully',
                'status' => 201
            ];
        } else {
            return [
                'message' => 'Failed to create cart',
                'status' => 500
            ];
        }
    }

    public function deleteCart($id, $user_id)
    {
        $cart = $this->getCartById($id);
        if ($cart && $cart->user_id == $user_id) {
            $this->cartRepository->delete($id);
            $this->bookService->increaseAvailableCopies($cart->book_id);
            $this->bookService->updateBookCopy($cart->copy_id, "available");
            return  [
                'message' => 'Delete cart successfully',
                'status' => 200
            ];
        }
        return [
            'message' => 'Not permission to delete cart',
            'status' => 400
        ];
    }

    public function clearCarts($user_id)
    {
        $this->cartRepository->clearCarts($user_id);
    }

    public function existsCart($book_id, $user_id)
    {
        $cart = $this->cartRepository->existsCart($book_id, $user_id);
        return $cart;
    }

    public function getCartById($id)
    {
        return $this->cartRepository->getById($id);
    }
}
