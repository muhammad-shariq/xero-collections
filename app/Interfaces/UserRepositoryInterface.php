<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): User|array;
    public function findByEmail(string $email): ?User;
    public function findByName(string $name): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): void;
    public function findExistEmailValidate(string $email, int $id): Collection;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
}
