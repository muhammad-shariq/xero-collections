<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\CollectionInvoiceService;
use App\Common\Xero2Api;
use App\Services\EmailTemplateService;
use App\Services\ContactService;

class DashboardController extends Controller
{
    private $collectionInvoiceService;
    private $emailTemplateService;
    private $contactService;

    public function __construct(CollectionInvoiceService $collectionInvoiceService, EmailTemplateService $emailTemplateService, ContactService $contactService )
    {
        $this->collectionInvoiceService = $collectionInvoiceService;
        $this->emailTemplateService = $emailTemplateService;
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        $xero = new Xero2Api($request->user());

        $xero_overdue_invoice = $xero->getCountXeroInvoicesData(true, config('common.authorized'));
        // $xero_approval_invoice = $xero->getCountXeroInvoicesData(true, config('common.submitted'));
        $xero_overdue_invoice_data = $xero->getCountXeroInvoicesData(false, config('common.authorized'), 100);

        $xero_amount_due = 0;
        if( isset($xero_overdue_invoice_data['Invoices']) && count($xero_overdue_invoice_data['Invoices']) > 0 )
            foreach($xero_overdue_invoice_data['Invoices'] as $invoice)
                $xero_amount_due += $invoice['Total'];

        $email_template_data = [];
        $email_template_service_data = $this->emailTemplateService->getEmailTemplateData($request->user()->id);

        foreach($email_template_service_data as $key => $template )
        {
            $email_template_data[$key] = $template;
            $email_template_data[$key]['invoice_data'] = $this->contactService->getContactInvoiceEmailCollection($template['id']);
            $email_template_data[$key]['amount_paid'] = 0;
            $email_template_data[$key]['import_amount_paid'] = 0;
            if( count($email_template_data[$key]['invoice_data']) > 0 )
            {
                $email_template_data[$key]['amount_paid'] = $this->collectionInvoiceService->getSumAmountPaid($email_template_data[$key]['invoice_data'][0]['ref_contact_id']);
                $email_template_data[$key]['import_amount_paid'] = $this->collectionInvoiceService->getImportSumAmountPaid($email_template_data[$key]['invoice_data'][0]['ref_contact_id']);
            }
        }
        // dump($email_template_data);

        $collection_invoices = $this->collectionInvoiceService->getContactsStats();

        $amount_due = $this->collectionInvoiceService->getSumAmountDue();
        $amount_paid = $this->collectionInvoiceService->getSumAmountPaid();
        $import_amount_paid = $this->collectionInvoiceService->getImportSumAmountPaid();

        $data = [];
        foreach($collection_invoices as $k => $val){
            $data[0]['name'] = 'Amount Due';
            $data[0]['data'][$k]['x'] = $val['contact_name'];
            $data[0]['data'][$k]['y'] = $val['amount_due'];

            $data[1]['name'] = 'Amount Paid';
            $data[1]['data'][$k]['x'] = $val['contact_name'];
            $data[1]['data'][$k]['y'] = $val['amount_paid'];
        }

        return Inertia::render('Dashboard', [
            'data' => $data,
            'amount_due' => (float) ($amount_due - $amount_paid),
            'amount_paid' => (float) ($amount_paid - $import_amount_paid),
            'xero_overdue_invoice' => (int) $xero_overdue_invoice,
            'xero_amount_due' => (float) $xero_amount_due,
            'email_template_data' => $email_template_data
        ]);
    }
}
