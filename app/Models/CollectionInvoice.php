<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;

class CollectionInvoice extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'contact_id',
        'contact_email',
        'contact_name',
        'invoice_number',
        'reference',
        'amount_due',
        'amount_paid',
        'import_amount_paid',
        'invoice_date',
        'due_date',
        'last_email',
        'user_id',
    ];

    /**
     * Get the user that owns the collection invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    static function getInvoiceContactData(int $invoice_id = 0)
    {
        return CollectionInvoice::with('getContacts')
                ->where('id', $invoice_id)->get()->toArray();
    }

    public function getContacts()
    {
        return $this->BelongsTo(Contact::class, 'contact_id', 'ref_contact_id');
    }

    static function getUserDueInvoice($days)
    {
        $last_email_date = Carbon::now()->subDays($days['trigger_days']);
        $email_date = Carbon::now()->addDays($days['trigger_days']);

        // \DB::enableQueryLog();

        return CollectionInvoice::where([
                ['contact_email', '!=', ""],
            ])->whereDate('due_date', '<', $email_date)
            ->where(function($query) use ($last_email_date) {
                $query->whereDate('last_email', '=', $last_email_date)
                ->orWhereNull('last_email');
            })
            ->where('template_id','not like',"%--{$days['id']}--%")
            ->get()->toArray();
            // dd(\DB::getQueryLog());
    }

    protected function getContactsStats(): Collection
    {
        // \DB::enableQueryLog();
        // dump(Auth::id());
        return CollectionInvoice::select('contact_name', DB::raw('SUM(collection_invoices.amount_due) as amount_due'), DB::raw('SUM(collection_invoices.amount_paid) as amount_paid'))
            ->join('contacts', 'contacts.ref_contact_id', 'collection_invoices.contact_id')
            ->where('collection_invoices.user_id', Auth::id())
            ->groupBy('contact_id', 'contact_name')
            ->get();
        // dd(\DB::getQueryLog());
    }

    protected function getSumAmountDue(): int
    {
        return CollectionInvoice::where('user_id', Auth::id())
            ->sum('amount_due');
    }

    protected function getSumAmountPaid(string $contact_id = ""): int
    {
        $q = CollectionInvoice::where('user_id', Auth::id());
        if($contact_id !== "")
            $q = $q->where('contact_id', $contact_id);
        return $q->sum('amount_paid');
    }

    protected function getImportSumAmountPaid(string $contact_id = ""): int
    {
        $q = CollectionInvoice::where('user_id', Auth::id());
        if( $contact_id !== "" )
            $q = $q->where('contact_id', $contact_id);
        return  $q->sum('import_amount_paid');
    }

    protected function getContactsFromInvoices(int|null $user_id) :array
    {

        $invoices = CollectionInvoice::distinct('contact_id')
            ->where('import_contact', 0);
        if( $user_id )
            $invoices = $invoices->where('user_id', $user_id);
        $invoices = $invoices->get()->groupBy('user_id')->toArray();

        return $this->getContactIdOnly($invoices);
    }

    private function getContactIdOnly($invoices): array
    {
        $data = [];
        foreach($invoices as $k=>$val)
            foreach($val as $in_val)
                $data[$k][] = $in_val['contact_id'];
        return $data;
    }
}
