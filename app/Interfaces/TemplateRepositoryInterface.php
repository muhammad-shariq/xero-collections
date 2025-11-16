<?php

namespace App\Interfaces;

use App\Models\Template;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TemplateRepositoryInterface
{
    public function all(): Collection|array;
    public function find(int $id): Template;
    public function findByUserID(int $user_id): Collection;
    public function findByTemplateName(string $template_id): ?Template;
    public function create(array $data): Template;
    public function update(int $id, array $data): bool;
    public function delete(int $id): void;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
}
