<?php

namespace App\Repositories\Book;

use App\Repositories\BaseRepositoryInterface;

interface BookRepositoryInterface extends BaseRepositoryInterface
{
    public function getBooksByCategory($id, $size);
    public function restore($id);
}
