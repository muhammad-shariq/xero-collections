<?php

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection|array;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
}
