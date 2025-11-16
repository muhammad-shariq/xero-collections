<?php

namespace App\Interfaces;

use App\Models\EmailTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmailTemplateRepositoryInterface
{
    public function all(): Collection|array;
    public function find(int $id): EmailTemplate;
    public function findByTemplateID(string $template_id): ?EmailTemplate;
    public function findByTriggerDays(string $trigger_days): ?EmailTemplate;
    public function create(array $data): EmailTemplate;
    public function bulkInsert(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): void;
    public function paginate(int $perPage = 10, array $sort = [], array $filters = []): LengthAwarePaginator;
}
