<?php

namespace App\Observers;

use App\Models\CollectionInvoice;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\UserActivityService;

class CollectionInvoiceObserver
{

    private $userActivityService;

    public function __construct(UserActivityService $userActivityService)
    {
        $this->userActivityService = $userActivityService;
    }

    /**
     * Handle the CollectionInvoice "created" event.
     */
    public function created(CollectionInvoice $collectionInvoice): void
    {
        $data = [
            'activity_id' => 1,
            'invoice_id' => $collectionInvoice->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $this->userActivityService->insert($data);
    }

    /**
     * Handle the CollectionInvoice "updated" event.
     */
    public function updated(CollectionInvoice $collectionInvoice): void
    {
        //
    }

    /**
     * Handle the CollectionInvoice "deleted" event.
     */
    public function deleted(CollectionInvoice $collectionInvoice): void
    {
        //
    }

    /**
     * Handle the CollectionInvoice "restored" event.
     */
    public function restored(CollectionInvoice $collectionInvoice): void
    {
        //
    }

    /**
     * Handle the CollectionInvoice "force deleted" event.
     */
    public function forceDeleted(CollectionInvoice $collectionInvoice): void
    {
        //
    }
}
