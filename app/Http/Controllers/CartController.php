<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Services\CartService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends ControllerWithGuard
{
    use APIResponse;
    private $cartService;
    public function __construct(CartService $cartService)
    {
        parent::__construct();
        $this->cartService = $cartService;
    }

    public function getCartsOfUser() {
        $user = Auth::user();
        try {
            $carts = $this->cartService->getCartsOfUser($user->id);
            return $this->responseSuccessWithData($carts);
        } catch (\Exception $e) {
            return $this->responseError('Failed to get carts');
        }
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
    public function store(CartStoreRequest $request)
    {
        $user = Auth::user();
        $data = $request->only(['book_id', 'rental_fee']);
        $data['user_id'] = $user->id;
        $result = $this->cartService->createCart($data);
        if($result['status'] == 201) {
            return $this->responseSuccess($result['message'], $result['status']);
        }
        return $this->responseError($result['message'], $result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cart = $this->cartService->getCartById($id);
        if (!$cart) {
            return $this->responseError('Cart not found');
        }
        return $this->responseSuccessWithData($cart);
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
    public function destroy($id)
    {
        $user = Auth::user();
        $result = $this->cartService->deleteCart($id, $user->id);
        if($result['status'] == 200) {
            return $this->responseSuccess($result['message'], $result['status']);
        }
        return $this->responseError($result['message'], $result['status']);
    }   
}
