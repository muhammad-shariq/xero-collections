<?php
namespace App\Repositories;

use App\Models\CollectionInvoice;
use App\Interfaces\CollectionInvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CollectionInvoiceRepository implements CollectionInvoiceRepositoryInterface
{
    private $collectionInvoice;

    public function __construct(CollectionInvoice $collectionInvoice)
    {
        $this->collectionInvoice = $collectionInvoice;
    }

    public function update(int $id, array $data): bool
    {
        return CollectionInvoice::where('id', $id)->update($data);
    }

    public function all(): Collection|array
    {
        return CollectionInvoice::all()->toArray();
    }

    public function getCollectionInvoicesByContactID(string $contact_id, bool $multiple = false): CollectionInvoice|null|Collection
    {
        if( $multiple == true )
            return CollectionInvoice::where('contact_id', $contact_id)->get();

        return CollectionInvoice::where('contact_id', $contact_id)->first();
    }

    public function getCollectionInvoicesByInvoiceID(string $invoice_id): CollectionInvoice|null
    {
        return CollectionInvoice::where('invoice_id', $invoice_id)->first();
    }

    public function paginate(int $per_page = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->collectionInvoice->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);

        return $query->where('user_id', Auth::id())
            ->paginate($per_page)->withQueryString()->through(fn($ci) => [
                'id' => $ci->id,
                'invoice_id' => $ci->invoice_id,
                'contact_id' => $ci->contact_id,
                'contact_email' => $ci->contact_email,
                'contact_name' => $ci->contact_name,
                'invoice_number' => $ci->invoice_number,
                'reference' => $ci->reference,
                'amount_due' => $ci->amount_due,
                'amount_paid' => $ci->amount_paid,
                'invoice_date' => $ci->invoice_date,
                'due_date' => $ci->due_date,
        ]);
    }

    public function getTemplateInvoiceCollections(int $per_page = 15, array $sort = [], array $filters = [], int $template_id, bool $counter = false): LengthAwarePaginator
    {
        // return CollectionInvoice::select('collection_invoices.*')
        //     ->join('contacts', 'contacts.ref_contact_id', 'collection_invoices.contact_id')
        //     ->where('template_id','like', "%--$template_id--")
        //     ->get()->toArray();

        $query = $this->collectionInvoice->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);
        $query = $query->select("collection_invoices.*", "contacts.mobile_number")
                    ->join('contacts', 'contacts.ref_contact_id', 'collection_invoices.contact_id');

        if( $template_id > 0 )
        {
            $query = $query->where('template_id','like', "%--$template_id--");
        }

        if($counter)
        {
            $expDate = Carbon::now()->subDays(7);
            $condition = $template_id > 0 ? ">" : "<";
            $query = $query->whereDate('last_email', $condition, $expDate);
        }


        return $query->where('user_id', Auth::id())
                ->paginate($per_page)->withQueryString()->through(fn($ci) => [
                    'id' => $ci->id,
                    'invoice_id' => $ci->invoice_id,
                    'contact_id' => $ci->contact_id,
                    'contact_email' => $ci->contact_email,
                    'mobile_number' => $ci->mobile_number,
                    'contact_name' => $ci->contact_name,
                    'invoice_number' => $ci->invoice_number,
                    'reference' => $ci->reference,
                    'amount_due' => $ci->amount_due,
                    'amount_paid' => $ci->amount_paid,
                    'invoice_date' => $ci->invoice_date,
                    'due_date' => $ci->due_date,
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['keyword'])) {
            $query->where('invoice_id', 'like', "%{$filters['invoice_id']}%");
            $query->orWhere('contact_id', 'like', "%{$filters['contact_id']}%");
            $query->orWhere('contact_email', 'like', "%{$filters['contact_email']}%");
            $query->orWhere('contact_name', 'like', "%{$filters['contact_name']}%");
            $query->orWhere('invoice_number', 'like', "%{$filters['invoice_number']}%");
            $query->orWhere('reference', 'like', "%{$filters['reference']}%");
            $query->orWhere('amount_due', 'like', "%{$filters['amount_due']}%");
            $query->orWhere('amount_paid', 'like', "%{$filters['amount_paid']}%");
            $query->orWhere('invoice_date', 'like', "%{$filters['invoice_date']}%");
            $query->orWhere('due_date', 'like', "%{$filters['due_date']}%");
        }

        if(isset($filters['filter_by']))
        {
            if($filters['filter_by'] == 1)
                $query->where('due_date', '<', Carbon::now());
            elseif($filters['filter_by'] == 2 )
                $query->where('due_date', '>=', Carbon::now());

        }
        // Add more filters as needed
    }

    protected function applySorting(Builder $query, array $sort): void
    {
        if (empty($sort)) {
            return;
        }

        $sortField = $sort['field'] ?? 'id'; // Default to 'id' if not specified
        $sortDirection = $sort['direction'] ?? 'asc'; // Default to ascending

        $query->orderBy($sortField, $sortDirection);

        // Handle complex sorting with multiple columns using DB::raw (optional)
        // if (count($sort) > 1) {
        //     $sortSql = '';
        //     foreach ($sort as $field => $direction) {
        //         $sortSql .= "{$field} {$direction}, ";
        //     }
        //     $sortSql = rtrim($sortSql, ', ');
        //     $query->orderByRaw($sortSql);
        // }
    }

    public function getContactsFromInvoices(int|null $user_id): array
    {
        return CollectionInvoice::getContactsFromInvoices($user_id);
    }

    public function getContactsStats(): Collection
    {
        return CollectionInvoice::getContactsStats();
    }

    public function delete(int|array $contacts): void
    {
        $query = $this->collectionInvoice->newQuery();
        if(gettype($contacts) == "array")
            $query->whereIn('contact_id', $contacts);
        else
            $query->where('id', $contacts);

        $query->delete();
    }

    public function deleteCollectionViaInvoiceID(string $invoice_id): void
    {
        CollectionInvoice::where('invoice_id', $invoice_id)->delete();
    }

    public function getSumAmountDue(): int
    {
        return CollectionInvoice::getSumAmountDue();
    }

    public function getSumAmountPaid(string $contact_id = ""): int
    {
        return CollectionInvoice::getSumAmountPaid($contact_id);
    }

    public function getImportSumAmountPaid(string $contact_id = ""): int
    {
        return CollectionInvoice::getImportSumAmountPaid($contact_id);
    }

    public function bulkInsert(array $data): bool
    {
        return CollectionInvoice::insert($data);
    }

    public function create(array $data): CollectionInvoice
    {
        return CollectionInvoice::create($data);
    }

    public function bulkContactUpdate(array $ids, array $data): bool
    {
        return CollectionInvoice::whereIn('contact_id', $ids)->update($data);
    }

}
