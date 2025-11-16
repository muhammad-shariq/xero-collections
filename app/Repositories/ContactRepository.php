<?php
namespace App\Repositories;

use App\Models\Contact;
use App\Interfaces\ContactRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
class ContactRepository implements ContactRepositoryInterface
{
    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function all(): Collection
    {
        return $this->contact->all();
    }

    public function find(int $id): Contact
    {
        return $this->contact->findOrFail($id);
    }

    public function getUserDueInvoice(array $data) :array
    {
        return Contact::getUserDueInvoice($data);
    }

    public function findByEmail(string $email): ?Contact
    {
        return $this->contact->where('email', $email)->first();
    }

    public function findByRefContactID(string $ref_contact_id): Contact|null
    {
        return $this->contact->where('ref_contact_id', $ref_contact_id)->first();
    }

    public function create(array $data): Contact
    {
        return $this->contact->create($data);
    }

    public function bulkInsert(array $data): bool
    {
        return Contact::insert($data);
    }

    public function update(int $id, array $data): bool
    {
        return Contact::where('id', $id)->update($data);
        // $contact = $this->find($id);
        // $contact->update($data);
        // return $contact;
    }

    public function getContactInvoiceEmailCollection(int $template_id)
    {
        // $query = $this->contact->newQuery();
        return Contact::select('contacts.*', 'collection_invoices.amount_paid', 'collection_invoices.import_amount_paid')->where('template_id', 'like' ,"%--{$template_id}--")
            ->join('collection_invoices', 'collection_invoices.contact_id', 'ref_contact_id')
            ->get()->toArray();
    }

    // public function delete(int $id): void
    // {
    //     $this->find($id)->delete();
    // }

    public function delete(int|array $contacts): void
    {
        $query = $this->contact->newQuery();
        if(gettype($contacts) == "array")
            $query->whereIn('ref_contact_id', $contacts);
        else
            $query->where('id', $contacts);

        $query->delete();
    }

    public function paginate(int $per_page = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->contact->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);

        return $query->select('contacts.*')->join('collection_invoices', 'collection_invoices.contact_id', '=', 'contacts.ref_contact_id')
            ->where('collection_invoices.user_id', Auth::id())
            ->distinct()
            ->paginate($per_page)->withQueryString()->through(fn($ci) => [
            'id' => $ci->id,
            'ref_contact_id' => $ci->contact_id,
            'name' => $ci->name,
            'email' => $ci->email,
            'ref_contact_id' => $ci->ref_contact_id,
            'mobile_number' => $ci->mobile_number,
            'contact_status' => $ci->contact_status,
            'ar_outstanding' => $ci->ar_outstanding,
            'ar_overdue' => $ci->ar_overdue,
            'updated_at' => $ci->updated_at->toDateTimeString(),
            'created_at' => $ci->created_at->toDateTimeString(),
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['keyword'])) {
            $query->where('name', 'like', "%{$filters['keyword']}%");
            $query->orWhere('ref_contact_id', 'like', "%{$filters['keyword']}%");
            $query->orWhere('email', 'like', "%{$filters['keyword']}%");
        }
        if (isset($filters['ref_contact_id'])) {
            $query->where('ref_contact_id', 'like', "%{$filters['ref_contact_id']}%");
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
}
