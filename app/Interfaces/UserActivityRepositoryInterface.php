<?php

namespace App\Interfaces;
use App\Models\UserActivity;

interface UserActivityRepositoryInterface
{
    public function insert(array $data): UserActivity;

    public function bulkInsert(array $data): bool;
}
