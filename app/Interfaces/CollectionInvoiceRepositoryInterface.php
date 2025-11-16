<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CollectionInvoiceRepositoryInterface
{
    public function bulkInsert(array $data): bool;
    public function update(int $id, array $data): bool;
    public function all(): Collection|array;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
    public function delete(int|array $id): void;
}
