<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function getAll($size, $q);
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
