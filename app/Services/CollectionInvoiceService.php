<?php

namespace App\Services;

use App\Models\CollectionInvoice;
use App\Repositories\CollectionInvoiceRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CollectionInvoiceService{

    private $collectionInvoiceRepository;

    public function __construct(CollectionInvoiceRepository $collectionInvoiceRepository)
    {
        $this->collectionInvoiceRepository = $collectionInvoiceRepository;
    }

    public function update($id, $update): bool
    {
        return $this->collectionInvoiceRepository->update($id, $update);
    }

    public function getAll($pagination = false, int $perPage = 15, array $sort = [], array $filters = []){
        if($pagination){
            return $this->collectionInvoiceRepository->paginate($perPage, $sort, $filters);
        }
        return $this->collectionInvoiceRepository->all();
    }

    public function getTemplateInvoiceCollections($pagination = false, int $perPage = 15, array $sort = [], array $filters = [], int $template_id, bool $counter = false)
    {
        if($pagination){
            return $this->collectionInvoiceRepository->getTemplateInvoiceCollections($perPage, $sort, $filters, $template_id, $counter);
        }
        return $this->collectionInvoiceRepository->getTemplateInvoiceCollections(100, [], [], $template_id, $counter);
    }

    public function getContactsStats(): array
    {
        return $this->collectionInvoiceRepository->getContactsStats()->toArray();
    }

    public function getCollectionInvoicesByContactID(string $contact_id, bool $multipe = false): array
    {
        $data = $this->collectionInvoiceRepository->getCollectionInvoicesByContactID($contact_id, $multipe);
        if($data !== null)
            return $data->toArray();
        return [];
    }

    public function getCollectionInvoicesByInvoiceID(string $invoice_id): array
    {
        $data = $this->collectionInvoiceRepository->getCollectionInvoicesByInvoiceID($invoice_id);
        if($data !== null)
            return $data->toArray();
        return [];
    }

    public function getSumAmountPaid(string $contact_id = ""): int
    {
        return $this->collectionInvoiceRepository->getSumAmountPaid($contact_id);
    }

    public function getImportSumAmountPaid(string $contact_id = ""): int
    {
        return $this->collectionInvoiceRepository->getImportSumAmountPaid($contact_id);
    }

    public function getSumAmountDue(): int
    {
        return $this->collectionInvoiceRepository->getSumAmountDue();
    }

    public function getContactsFromInvoices( int|null $user_id): array
    {
        return $this->collectionInvoiceRepository->getContactsFromInvoices($user_id);
    }

    public function deleteContacts(int|array $contacts)
    {
        $this->collectionInvoiceRepository->delete($contacts);
    }

    public function deleteCollectionViaInvoiceID(string $invoice_id) :void
    {
        $this->collectionInvoiceRepository->deleteCollectionViaInvoiceID($invoice_id);
    }

    public function bulkInsert(array $data): bool
    {
        return $this->collectionInvoiceRepository->bulkInsert($data);
    }

    public function save(array $data): CollectionInvoice
    {
        return $this->collectionInvoiceRepository->create($data);
    }

    public function bulkContactUpdate(array $ids, array $data): bool
    {
        return $this->collectionInvoiceRepository->bulkContactUpdate($ids, $data);
    }

}
