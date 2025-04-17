<?php

namespace App\Repositories\Cart;

use App\Repositories\BaseRepositoryInterface;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function existsCart($book_id, $user_id);
    public function getCartsOfUser($user_id);
    public function clearCarts($user_id);
}
