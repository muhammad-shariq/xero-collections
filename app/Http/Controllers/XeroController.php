<?php

namespace App\Http\Controllers;

use App\Common\Xero2Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Xero2Api;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use App\Services\CollectionInvoiceService;
use App\Services\ContactService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Helpers\CustomHelper;

class XeroController extends Controller
{

    private $collectionInvoiceService;
    private $contactService;
    private $customHelper;

    public function __construct(CollectionInvoiceService $collectionInvoiceService, ContactService $contactService, CustomHelper $customHelper)
    {
        $this->collectionInvoiceService = $collectionInvoiceService;
        $this->contactService = $contactService;
        $this->customHelper = $customHelper;
    }

    public function authorise(Request $request): RedirectResponse
    {
        $request->user();
        $xero = new Xero2Auth();
        $data = $xero->connect();

        request()->session()->put('xero_state', $data['oauth2_state']);
        return redirect($data['authorization_url']);
    }

    public function callback( Request $request )
    {
        $request->validate([
            'code' => ['required', 'max:255'],
            'state' => ['required', 'max:255'],
        ]);

        try
        {
            $xero_state = $request->session()->get('xero_state');

            if( $xero_state !== $request->state )
                throw new \Exception('Unable to verify state.');

            $xero = new Xero2Auth();
            $result = $xero->get_access_token($request->code);

            if( $result['status'] !== 'SUCCESS' )
            {
                $request->session()->flash('alert-danger', $result['error']);
                return redirect()->route('dashboard');
            }
            $user = $request->user();
            $user->xero_access_token = $result['data']['token'];
            $user->xero_refresh_token = $result['data']['refresh_token'];
            $user->xero_token_expiry = $result['data']['expiry'];
            $user->xero_tenant_id = $result['data']['tenant_id'];
            $user->save();

            $xero_data = new Xero2Api($request->user());
            $organization_data = $xero_data->getOrganisations();

            if( $organization_data['status'] !== 'SUCCESS' )
            {
                $request->session()->flash('alert-danger', $organization_data['error']);
                return redirect()->route('dashboard');
            }

            $user->xero_organization_name = $organization_data['data']->getOrganisations()[0]->getName();
            $user->save();

            $request->session()->flash('xerosuccess', "successful");
            return redirect()->route('profile.edit');
        }
        catch(QueryException $e)
        {
            dd($e);
        }
        catch (\Exception $e)
        {
            $request->session()->flash('xeroerror', $e->getmessage());
            return redirect()->route('profile.edit');
        }
    }

    public function webhook(Request $request)
    {
        $computedSignatureKey = $this->customHelper->hash_hmac_base64_decode($request->getContent());
		$xeroSignatureKey = $request->header('x-xero-signature');

		if (hash_equals($computedSignatureKey, $xeroSignatureKey)) {

			if( isset($request->input()['events']) && count($request->input()['events']) > 0 )
            {
                foreach($request->input()['events'] as $event)
                {
                    if ( $event['eventType'] === config("common.create_event_type") && $event['eventCategory'] === config("common.invoice_event_category") ) {
                        $this->invoiceCreated($event);
                    } elseif ( $event['eventType'] === config("common.create_event_type") && $event['eventCategory'] === config("common.contact_event_category") ) {
                        $this->contactCreated($event);
                    } elseif ( $event['eventType'] === config("common.update_event_type") && $event['eventCategory'] === config("common.invoice_event_category") ) {
                        $this->invoiceUpdated($event);
                    } elseif ( $event['eventType'] === config("common.update_event_type") && $event['eventCategory'] === config("common.contact_event_category") ) {
                        $this->contactUpdated($event);
                    }
                }
			}
			return response('', Response::HTTP_OK);
		}
		return response('', Response::HTTP_UNAUTHORIZED);
    }

    protected function invoiceCreated(array $invoice)
    {
    }

    protected function contactCreated(array $contact)
    {
    }

    protected function invoiceUpdated(array $invoice)
    {
        $collection_data = $this->collectionInvoiceService->getCollectionInvoicesByInvoiceID($invoice['resourceId']);
        if( count($collection_data) > 0 )
        {
            $xero = new Xero2Api(Auth::loginUsingId($collection_data['user_id']));
            $contact_xero_data = $xero->getInvoices(null, null, null, $invoice['resourceId']);
            if( isset($contact_xero_data['status']) && strtolower($contact_xero_data['status']) == "success")
            {
                $update = [
                    'amount_paid' => $contact_xero_data['data'][0]['amount_paid'],
                    'amount_due' => $contact_xero_data['data'][0]['amount_due'],
                    'updated_at' => Carbon::now()
                ];

                $this->collectionInvoiceService->update($collection_data['id'], $update);
                // dump($contact_xero_data['data'][0]['amount_paid']);
                // dd($contact_xero_data);
            }
        }
    }

    protected function contactUpdated(array $contact)
    {
        $collection_data = $this->collectionInvoiceService->getCollectionInvoicesByContactID($contact['resourceId']);
        if( count($collection_data) > 0 )
        {
            $contact_db_data = $this->contactService->findByRefContactID($contact['resourceId']);
            if( count($contact_db_data) > 0 )
            {
                $xero = new Xero2Api(Auth::loginUsingId($collection_data['user_id']));
                $contact_xero_data = $xero->getContact($contact['resourceId']);

                if( isset($contact_xero_data['status']) && strtolower($contact_xero_data['status']) == "success")
                {
                    $contacts = [];
                    $key = 0;
                    $inner_val = $contact_xero_data['data'];
                    $contacts[$key]['ref_contact_id'] = $inner_val->getContactID();
                    $contacts[$key]['name'] = $this->customHelper->check_null($inner_val->getName());
                    $contacts[$key]['email'] = $this->customHelper->check_null($inner_val->getEmailAddress());
                    $contacts[$key]['first_name'] = $this->customHelper->check_null($inner_val->getFirstName());
                    $contacts[$key]['last_name'] = $this->customHelper->check_null($inner_val->getLastName());
                    $contacts[$key]['mobile_number'] = $this->customHelper->check_null($inner_val->getPhones()[3]['phone_type']);
                    $contacts[$key]['ar_outstanding'] = $inner_val->getBalances()['accounts_receivable']['outstanding'];
                    $contacts[$key]['ar_overdue'] = $inner_val->getBalances()['accounts_receivable']['overdue'];
                    $contacts[$key]['updated_at'] = $contacts[$key]['created_at'] = Carbon::now();
                    $contacts[$key]['contact_status'] = $this->customHelper->check_null(strtolower($inner_val->getContactStatus())) == "active" ? 1: 0;
                    $contacts[$key]['json_data'] = $inner_val;
                    $contacts[$key]['template_id'] = $contact_db_data['template_id'];
                    $contacts[$key]['last_email'] = $contact_db_data['last_email'];

                    $this->contactService->deleteContacts($contact_db_data['id']);
                    $this->contactService->bulkInsert($contacts);
                }
            }
        }
    }

    public function getInvoices (Request $request)
    {
        // dd(Carbon::now()->format('d M Y'));

        return Inertia::render('Invoice/List');
    }

    public function getInvoiceData(Request $request)
    {
        $page = $request->page?? 1;
        $xero = new Xero2Api($request->user());
        $filter = 'AmountDue>0';
        if( $request->filter_term == 1 ){
            $filter .= " AND DueDate< DateTime(".Carbon::now()->format('Y,m,d').") ";
        }elseif( $request->filter_term == 2 ){
            $filter .= " AND DueDate>= DateTime(".Carbon::now()->format('Y,m,d').") ";
        }

        $order_by = $request->sort_by == "" ? "" : $request->sort_by." ".$request->sort_dir;

        if( !empty($request->search_term) )
            $filter .= ' AND (InvoiceNumber='.$request->search_term.' OR Reference='.$request->search_term.')';

        $data = $xero->getInvoices($filter, $order_by, null, null, null, null, "AUTHORISED", $page);

        $invoices = [];
        if($data['status'] === 'SUCCESS')
        {
            $now = Carbon::now()->format("Y-m-d");
            foreach ($data['data'] as $k => $invoice)
            {
                $contact =  $invoice['contact'];
                $invoices[$k] = [
                    'invoice_id' => $invoice['invoice_id'],
                    'contact_id' => $contact->getContactId(),
                    'contact_name' => $contact->getName(),
                    'contact_email' => $contact->getEmailAddress(),
                    'invoice_number' => $invoice['invoice_number'],
                    'reference' => $invoice['reference'],
                    'status' => $invoice['status'],
                    'amount_due' => $invoice['amount_due'],
                    'amount_paid' => $invoice['amount_paid'],
                    'date' => $invoice->getDateAsDate()->format('d M Y'),
                    'due_date' => $invoice->getDueDateAsDate()->format('d M Y'),
                    'isActiveRow' => false
                ];
                $days_over_due = new Carbon($invoice->getDueDateAsDate()->format('Y-m-d'));
                $difference = $days_over_due->diffInDays($now);
                $invoices[$k]['days_over_due'] = abs($difference);
            }
        }
        return $invoices;
    }
}
