<?php
namespace App\Common;

use App\Models\User;
use Carbon\Carbon;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Models\Accounting\Account;
use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\Contacts;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;
use XeroAPI\XeroPHP\Models\Accounting\Invoices;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;
use XeroAPI\XeroPHP\Models\Accounting\Address;
use XeroAPI\XeroPHP\Models\Accounting\Item;
use XeroAPI\XeroPHP\Models\Accounting\Items;
use XeroAPI\XeroPHP\Models\Accounting\LineItemTracking;
use XeroAPI\XeroPHP\Models\Accounting\Purchase;
use GuzzleHttp\Client;
use XeroAPI\XeroPHP\ApiException;
use XeroAPI\XeroPHP\Configuration;
use Exception;
use XeroAPI\XeroPHP\Models\Accounting\Payment;
use XeroAPI\XeroPHP\Models\Accounting\Payments;
use XeroAPI\XeroPHP\Models\Accounting\Phone;
use XeroAPI\XeroPHP\Models\Accounting\RequestEmpty;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use XeroAPI\XeroPHP\Models\Accounting\TrackingCategory;

class Xero2Api
{
    protected $accountingApi, $access_token, $refresh_token, $expiry, $tenant_id;
    protected $user;

    public function __construct( User $user )
    {
        $this->user = $user;
        $this->access_token = $user->xero_access_token;
        $this->refresh_token = $user->xero_refresh_token;
        $this->expiry = Carbon::parse( date("Y-m-d H:i:s", $user->xero_token_expiry));
        $this->tenant_id = $user->xero_tenant_id;

        $config = Configuration::getDefaultConfiguration()->setAccessToken( $this->access_token );
        $this->accountingApi = new AccountingApi(
            new Client(),
            $config
        );
    }

    public function getOrganisations()
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }
            // Get Organisation details
            $response = $this->accountingApi->getOrganisations($this->tenant_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getCurrencies( $where = null, $order = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getCurrencies($this->tenant_id, $where, $order);
            return [
                'status' => config('common.success'),
                'data' => $response->getCurrencies()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAllItems( $where = NULL)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getItems($this->tenant_id, null, $where);
            return [
                'status' => config('common.success'),
                'data' => $response->getItems()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAllAccounts( $where = NULL )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getAccounts($this->tenant_id, null, $where, 'UpdatedDateUTC');
            return [
                'status' => config('common.success'),
                'data' => $response->getAccounts()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getBrandingThemes( )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getBrandingThemes($this->tenant_id);
            return [
                'status' => config('common.success'),
                'data' => $response->getBrandingThemes()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTaxRates($where = null, $order = null, $tax_type = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getTaxRates($this->tenant_id, $where, $order, $tax_type);
            return [
                'status' => config('common.success'),
                'data' => $response->getTaxRates()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getContact( $contact_id )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getContact($this->tenant_id, $contact_id);
            $contact = [];
            if($response->count()>0)
                $contact = $response->getContacts()[0];
            return [
                'status' => config('common.success'),
                'data' => $contact
            ];

        } catch (ApiException $e){
            if( $e->getCode() === 404 )
                return [
                    'status' => config('common.failed'),
                    'error' => $e->getResponseBody()
                ];

            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getContacts( $where = null, $order = null, $if_modified_since = null, $i_ds = null, $page = null, $include_archived = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getContacts($this->tenant_id, $if_modified_since, $where, $order, $i_ds, $page, $include_archived);
            return [
                'status' => config('common.success'),
                'data' => $response->getContacts()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function saveContact( array $data )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            if( isset($data['addresses']) && !empty($data['addresses']) )
            {
                $addresses = [];
                foreach ($data['addresses'] as $a)
                    $addresses[] = new Address($a);
                $data['addresses'] = $addresses;
            }
            if( isset($data['phones']) && !empty($data['phones']) )
            {
                $phones = [];
                foreach ($data['phones'] as $p)
                    $phones[] = new Phone($p);
                $data['phones'] = $phones;
            }
            $contact = new Contact($data);
            $arr_contacts = [];
            array_push($arr_contacts, $contact);

            $contacts = new Contacts;
            $contacts->setContacts($arr_contacts);

            // Get Organisation details
            //updateOrCreateContacts
            $response = $this->accountingApi->createContacts($this->tenant_id, $contacts);
            $contacts = $response->getContacts();
            if( isset($contacts[0]['has_validation_errors']) && $contacts[0]['has_validation_errors'] )
                return [
                    'status' => config('common.failed'),
                    'error' => $contacts[0]->getValidationErrors()[0]['message']
                ];
            return [
                'status' => config('common.success'),
                'data' => $contacts
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function saveItem( array $data )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            if( isset($data['purchase_details']) && !empty($data['purchase_details']) )
                $data['purchase_details'] = new Purchase($data['purchase_details']);

            if( isset($data['sales_details']) && !empty($data['sales_details']) )
                $data['sales_details'] = new Purchase($data['sales_details']);

            $item = new Item($data);
            $arr_items = [];
            array_push($arr_items, $item);

            $items = new Items;
            $items->setItems($arr_items);

            // Get Organisation details
            $response = $this->accountingApi->createItems($this->tenant_id, $items);
            $items = $response->getItems();

            if( isset($items[0]['validation_errors']) && $items[0]['validation_errors'] )
                return [
                    'status' => config('common.failed'),
                    'error' => $items[0]->getValidationErrors()[0]['message']
                ];
            return [
                'status' => config('common.success'),
                'data' => $items
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getInvoices( $where = null, $order = null, $if_modified_since = null, $i_ds = null, $invoice_numbers = null, $contact_i_ds = null, $statuses = null, $page = null, $include_archived = null, $created_by_my_app = null, $unitdp = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getInvoices($this->tenant_id, $if_modified_since, $where, $order, $i_ds, $invoice_numbers, $contact_i_ds, $statuses, $page, $include_archived, $created_by_my_app, $unitdp);
            return [
                'status' => config('common.success'),
                'data' => $response->getInvoices()
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getInvoices( $where, $order, $if_modified_since, $i_ds, $invoice_numbers, $contact_i_ds, $statuses, $page, $include_archived, $created_by_my_app, $unitdp);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getOnlineInvoice( $invoice_id)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getOnlineInvoice($this->tenant_id, $invoice_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getInvoiceAsPdf( $invoice_id)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getInvoiceAsPdf($this->tenant_id, $invoice_id, "application/pdf");
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getContactAttachmentById( $contact_id, $attachment_id, $content_type)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getContactAttachmentById($this->tenant_id, $contact_id, $attachment_id, $content_type);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getInvoiceAttachments( $invoice_id): array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getInvoiceAttachments($this->tenant_id, $invoice_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getInvoiceAttachments( $invoice_id);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getInvoiceAttachmentById($invoice_id, $attachment_id, $content_type)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getInvoiceAttachmentById($this->tenant_id, $invoice_id, $attachment_id, $content_type);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getInvoiceAttachmentById($invoice_id, $attachment_id, $content_type);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getCreditNoteAttachments( $credit_note_id): array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getCreditNoteAttachments($this->tenant_id, $credit_note_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getCreditNoteAttachments( $credit_note_id);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getCreditNoteAttachmentById($credit_note_id, $attachment_id, $content_type)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getCreditNoteAttachmentById($this->tenant_id, $credit_note_id, $attachment_id, $content_type);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getCreditNoteAttachmentById($credit_note_id, $attachment_id, $content_type);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getManualJournalAttachments( $manual_journal_id): array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getManualJournalAttachments($this->tenant_id, $manual_journal_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getManualJournalAttachments( $manual_journal_id);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getManualJournalAttachmentById($manual_journal_id, $attachment_id, $content_type)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getManualJournalAttachmentById($this->tenant_id, $manual_journal_id, $attachment_id, $content_type);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }
        catch (ApiException $e)
        {
            if( $e->getCode() == 429 )
            {
                $headers = $e->getResponseHeaders();
                sleep($headers['Retry-After'][0]);
                return $this->getManualJournalAttachmentById($manual_journal_id, $attachment_id, $content_type);
            }
            else
            {
                $response = json_decode($e->getResponseBody(), true);
                return [
                    'status' => config('common.failed'),
                    'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
                ];
            }
        }
    }

    public function getBankTransactionAttachments( $bank_transaction_id): array
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getBankTransactionAttachments($this->tenant_id, $bank_transaction_id);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getBankTransactionAttachmentById($bank_transaction_id, $attachment_id, $content_type)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getBankTransactionAttachmentById($this->tenant_id, $bank_transaction_id, $attachment_id, $content_type);
            return [
                'status' => config('common.success'),
                'data' => $response
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function saveInvoice( array $data )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }
            if( isset($data['contact_id']) && !empty($data['contact_id']) )
            {
                $data['contact'] = new Contact(['contact_id' => $data['contact_id']]);
                unset($data['contact_id']);
            }

            $line_items = [];
            if( isset($data['line_items']) && !empty($data['line_items']) )
            {
                foreach ($data['line_items'] as $v)
                {
                    if( isset($v['tracking']) && !empty($v['tracking']) )
                    {
                        $trackings = $v['tracking'];
                        $v['tracking'] = [];
                        foreach ($trackings as $tk => $tc) {
                            $v['tracking'][$tk] = new LineItemTracking([
                                'tracking_category_id' => $tc['tracking_category_id'],
                                // 'name' => $tc['name'],
                                'option' => $tc['option']
                            ]);
                        }
                    }
                    $line_items[] = new LineItem($v);
                }
                $data['line_items'] = $line_items;
            }

            $invoice = new Invoice($data);
            $arr_invoices = [];
            array_push($arr_invoices, $invoice);

            $invoices = new Invoices;
            $invoices->setInvoices($arr_invoices);

            // Get Organisation details
            $response = $this->accountingApi->updateOrCreateInvoices($this->tenant_id, $invoices);
            $invoices = $response->getInvoices();

            if( $invoices[0]->getHasErrors() )
                return [
                    'status' => config('common.failed'),
                    'error' => $invoices[0]->getValidationErrors()[0]['message']
                ];
            return [
                'status' => config('common.success'),
                'data' => $invoices
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getPayments($where = null, $order = null, $if_modified_since = null, $page = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getPayments($this->tenant_id, $if_modified_since, $where, $order, $page);
            return [
                'status' => config('common.success'),
                'data' => $response->getPayments()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getPrepayments( $where = null, $order = null, $if_modified_since = null, $page = null, $unitdp = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getPrepayments($this->tenant_id, $if_modified_since, $where, $order, $page, $unitdp);
            return [
                'status' => config('common.success'),
                'data' => $response->getPrepayments()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getOverpayments( $where = null, $order = null, $if_modified_since = null, $page = null, $unitdp = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getOverpayments($this->tenant_id, $if_modified_since, $where, $order, $page, $unitdp);
            return [
                'status' => config('common.success'),
                'data' => $response->getOverpayments()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getCreditNotes( $where = null, $order = null, $if_modified_since = null, $page = null, $unitdp = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getCreditNotes($this->tenant_id, $if_modified_since, $where, $order, $page, $unitdp);
            return [
                'status' => config('common.success'),
                'data' => $response->getCreditNotes()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getBankTransactions( $where = null, $order = null, $if_modified_since = null, $page = null, $unitdp = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getBankTransactions($this->tenant_id, $if_modified_since, $where, $order, $page, $unitdp);
            return [
                'status' => config('common.success'),
                'data' => $response->getBankTransactions()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            $error = NULL;
            if( !empty($response) )
            {
                if(isset($response['Detail']))
                    $error = $response['Detail'];
                elseif (isset($response['Message']))
                    $error = $response['Message'];
            }
            else
                $error = $e->getResponseBody();
            return [
                'status' => config('common.failed'),
                'error' => $error
            ];
        }
    }

    public function getManualJournals( $where = null, $order = null, $if_modified_since = null, $page = null)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getManualJournals($this->tenant_id, $if_modified_since, $where, $order, $page);
            return [
                'status' => config('common.success'),
                'data' => $response->getManualJournals()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getManualJournal( $manual_journal_id )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getManualJournal($this->tenant_id, $manual_journal_id);
            $journals = [];
            if($response->count()>0)
                $journals = $response->getManualJournals()[0];
            return [
                'status' => config('common.success'),
                'data' => $journals
            ];

        } catch (ApiException $e){
            if( $e->getCode() === 404 )
                return [
                    'status' => config('common.failed'),
                    'error' => $e->getResponseBody()
                ];

            $response = json_decode($e->getResponseBody(), true);
            $error = NULL;
            if( !empty($response) )
            {
                if(isset($response['Detail']))
                    $error = $response['Detail'];
                elseif (isset($response['Message']))
                    $error = $response['Message'];
            }
            else
                $error = $e->getResponseBody();
            return [
                'status' => config('common.failed'),
                'error' => $error
            ];
        }
    }

    public function savePayment( array $data )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }
            if( isset($data['invoice_id']) && !empty($data['invoice_id']) )
            {
                $data['invoice'] = new Invoice(['invoice_id' => $data['invoice_id']]);
                unset($data['invoice_id']);
            }
            if( isset($data['account_code']) && !empty($data['account_code']) )
            {
                $data['account'] = new Account(['code' => $data['account_code']]);
                unset($data['account_code']);
            }

            $payment = new Payment($data);
            $arr_payments = [];
            array_push($arr_payments, $payment);

            $payments = new Payments();
            $payments->setPayments($arr_payments);

            // Get Organisation details
            $response = $this->accountingApi->createPayments($this->tenant_id, $payments);
            $payments = $response->getPayments();
            if( $payments[0]->getHasValidationErrors() )
                return [
                    'status' => config('common.failed'),
                    'error' => $payments[0]->getValidationErrors()[0]['message']
                ];
            return [
                'status' => config('common.success'),
                'data' => $payments
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        }
    }

    public function getTrackingCategories( $where = null, $order = null, $include_archived = null )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            // Get Organisation details
            $response = $this->accountingApi->getTrackingCategories($this->tenant_id, $where, $order, $include_archived);
            return [
                'status' => config('common.success'),
                'data' => $response->getTrackingCategories()
            ];

        }catch (ApiException $e){
            $response = json_decode($e->getResponseBody(), true);
            return [
                'status' => config('common.failed'),
                'error' => isset($response['Detail'])?$response['Detail']: $response['Message']
            ];
        } catch (Exception $e) {
            return [
                'status' => config('common.failed'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function emailInvoice( $invoice_id )
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            $request_empty = new RequestEmpty();
            $this->accountingApi->emailInvoice($this->tenant_id, $invoice_id, $request_empty);
        }catch (ApiException $e){}
    }

    public function getCountXeroInvoicesData($count = true, $status = "", $pageSize = 1)
    {
        try
        {
            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            $response = Http::withToken($this->access_token)
                ->withHeaders([
                'Xero-Tenant-Id' => $this->tenant_id
            ])
            ->acceptJson()
            ->get(config('common.xero_api_invoice_url')."?Statuses={$status}&pageSize={$pageSize}");

            if($response->successful() )
            {
                if( $count === true )
                    return $response->collect()["pagination"]["itemCount"];

                return $response->collect();
            }

        }catch (Exception $e){
            if( $count == true )
                return 0;
            return $response->collect();
        }
    }

    public function updateContacts(array $data)
    {
        try{

            if( $this->expiry->lessThanOrEqualTo(Carbon::now()) )
            {
                $result = $this->refresh_token();
                if( $result['status'] !== config('common.success') )
                    return $result;
            }

            $response = Http::withToken($this->access_token)
                ->withHeaders([
                'Xero-Tenant-Id' => $this->tenant_id
            ])->acceptJson()
            ->post(config("common.xero_api_url").config("common.contacts")."/{$data['contact_id']}",[
                "Contacts" => [
                        [
                            "ContactID" => $data['contact_id'],
                            "Name" => $data['contact_name'],
                            "EmailAddress" => $data['contact_email'],
                            "Phones" => [
                                [
                                    "PhoneType" => config("common.mobile"),
                                    "PhoneNumber" => $data['mobile_number']
                                ]
                            ]
                        ]
                    ]
                ]);
            
            if($response->successful() )
            {
                return ['status' => config('common.success'), "data" => $response->collect()];
            }

            return ['status' => config('common.failed'), "data" => []];
        }catch (Exception $e){
            return ['status' => config('common.failed'), "data" => $e];
        }
    }

    protected function refresh_token()
    {
        $xero = new Xero2Auth();
        $response = $xero->get_new_access_token($this->refresh_token);
        if( $response['status'] === config('common.success') )
        {
            $this->user->xero_access_token = $this->access_token = $response['data']['token'];
            $this->user->xero_token_expiry = $response['data']['expiry'];
            $this->user->xero_refresh_token = $this->refresh_token = $response['data']['refresh_token'];
            $this->user->save();

            $this->expiry = Carbon::parse( date("Y-m-d H:i:s", $response['data']['expiry']) );

            $config = Configuration::getDefaultConfiguration()->setAccessToken( $this->access_token );
            $this->accountingApi = new AccountingApi(
                new Client(),
                $config
            );
            return [
                'status' => config('common.success'),
                'data' => [
                    'access_token' => $response['data']['token']
                ]
            ];
        }
        if( isset($response['error']) )
            Log::critical('ERROR Xero2Api.php:'.$response['error']);
        return [
            'status' => config('common.failed'),
            'error' => 'Unable to refresh the token.'
        ];
    }
}
