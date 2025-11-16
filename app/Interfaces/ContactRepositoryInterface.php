<?php

namespace App\Interfaces;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContactRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Contact;
    public function findByEmail(string $email): ?Contact;
    public function findByRefContactID(string $ref_contact_id): ?Contact;
    public function create(array $data): Contact;
    public function update(int $id, array $data): bool;
    public function delete(int|array $id): void;
    public function bulkInsert(array $data): bool;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
}
