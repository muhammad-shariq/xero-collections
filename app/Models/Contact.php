<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Contact extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'json_data',
    ];

    /**
     * Get the user that owns the collection invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function getUserDueInvoice(array $days): array
    {
        $last_email_date = Carbon::now()->subDays($days['trigger_days']);
        // $email_date = Carbon::now()->addDays($days['trigger_days']);

        return Contact::select('contacts.email', 'contacts.id as cont_id', 'last_email', 'template_id','collection_invoices.*')
            ->join('collection_invoices', 'collection_invoices.contact_id','contacts.ref_contact_id')
            // ->whereDate('due_date', '<', $email_date)
            // ->whereNotNull('contacts.email')
            ->where('contacts.email', '<>', '')
            ->where('user_id', $days['user_id'])
            ->where(function($query) use ($last_email_date) {
                $query->whereDate('last_email', '=', $last_email_date)
                ->orWhereNull('last_email');
            })
            ->where('template_id','not like',"%--{$days['id']}--%")
            ->orderBy('cont_id')
            ->get()->groupBy('cont_id')->toArray();

        // return $this->reOrderData($contacts);
    }

    private function reOrderData($data)
    {
        $final_data = [];
        foreach($data as $val){
            $final_data[$val['cont_id']][] = $val;
        }
        return $final_data;
    }
}
