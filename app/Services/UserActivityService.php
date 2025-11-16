<?php

namespace App\Services;
use App\Repositories\UserActivityRepository;
use App\Models\UserActivity;

class UserActivityService
{

    private $userActivityRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(UserActivityRepository $userActivityRepository)
    {
        $this->userActivityRepository = $userActivityRepository;
    }

    public function insert(array $data): UserActivity
    {
        return $this->userActivityRepository->insert($data);
    }

    public function bulkInsert(array $data): bool
    {
        return $this->userActivityRepository->bulkInsert($data);
    }

    public function getInvoiceHistory(int $invoice_id): array
    {
        return $this->userActivityRepository->getInvoiceHistory($invoice_id);
    }
}
