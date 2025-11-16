<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Common\Xero2Api;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\CollectionInvoiceService;
use App\Services\ContactService;

class addXeroContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-xero-contacts {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add contacts data from xero invoices for email';

    private $collectionInvoiceService;
    private $contactService;

    public function __construct(CollectionInvoiceService $collectionInvoiceService, ContactService $contactService)
    {
        parent::__construct();
        $this->collectionInvoiceService = $collectionInvoiceService;
        $this->contactService = $contactService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $invoices = $this->collectionInvoiceService->getContactsFromInvoices($userId);
        //dd($invoices);
        $contact_data = [];
        foreach($invoices as $k => $val){
            $xero = new Xero2Api(Auth::loginUsingId($k));
            $i_ds = implode(",", $val);
            // $i_ds = $val[0];
            $contact_data = $xero->getContacts( null, null, null, $i_ds, null, null);

            $contacts = [];
            if( strtolower($contact_data['status']) == "success")
            {
                foreach($contact_data['data'] as $key => $inner_val )
                {
                    $contacts[$key]['ref_contact_id'] = $inner_val->getContactID();
                    $contacts[$key]['name'] = $this->check_null($inner_val->getName());
                    $contacts[$key]['email'] = $this->check_null($inner_val->getEmailAddress());
                    $contacts[$key]['first_name'] = $this->check_null($inner_val->getFirstName());
                    $contacts[$key]['last_name'] = $this->check_null($inner_val->getLastName());
                    $contacts[$key]['mobile_number'] = $this->check_null($inner_val->getPhones()[3]['phone_type']);
                    $contacts[$key]['ar_outstanding'] = $inner_val->getBalances()['accounts_receivable']['outstanding'];
                    $contacts[$key]['ar_overdue'] = $inner_val->getBalances()['accounts_receivable']['overdue'];
                    $contacts[$key]['updated_at'] = $contacts[$key]['created_at'] = Carbon::now();
                    $contacts[$key]['contact_status'] = $this->check_null(strtolower($inner_val->getContactStatus())) == "active" ? 1: 0;
                    $contacts[$key]['json_data'] = $inner_val;
                }

                $this->contactService->deleteContacts($val);
                $this->contactService->bulkInsert($contacts);
                //if(  $this->contactService->bulkInsert($contacts) )
                //{
                    //$this->collectionInvoiceService->bulkContactUpdate($val, ['import_contact' => 1]);
                    // CollectionInvoice::whereIn('contact_id', $val)->update(['import_contact' => 1]);
                //}
            }
        }
        // dump("Contacts inserted successfully....redirecting");
    }

    function check_null($data)
    {
        if(is_null($data))
            return "";
        return $data;
    }
}
