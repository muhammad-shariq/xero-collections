<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollectionInvoice;
use App\Models\UserActivity;
use App\Services\CollectionInvoiceService;
use App\Services\ContactService;
use App\Services\UserActivityService;
use App\Services\EmailTemplateService;
use Exception;
use Inertia\Inertia;
use App\Jobs\InvoiceEmailJob;
use PDF;
use Carbon\Carbon;
use App\Common\Xero2Api;
use File;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Helpers\CustomHelper;
use Illuminate\Http\RedirectResponse;

class CollectionInvoiceController extends Controller
{
    private $collectionInvoiceService;
    private $emailTemplateService;
    private $contactService;
    private $customHelper;
    private $userActivityService;

    public function __construct(CollectionInvoiceService $collectionInvoiceService, EmailTemplateService $emailTemplateService, ContactService $contactService, CustomHelper $customHelper, UserActivityService $userActivityService)
    {
        $this->collectionInvoiceService = $collectionInvoiceService;
        $this->emailTemplateService = $emailTemplateService;
        $this->contactService = $contactService;
        $this->customHelper = $customHelper;
        $this->userActivityService = $userActivityService;
    }


    public function index(Request $request)
    {
        $per_page = $request->input('per_page')??10;
        $sort_by = $request->input('sort_by')??'id';
        $sort_dir = $request->input('sort_dir')??'desc';
        $filter_by = $request->input('selectFilter')??1;

        $filters = [
            'keyword' => $request->input('search'),
            'filter_by' => $filter_by
        ];
        $sort = [
            'field' => $sort_by,
            'direction' => $sort_dir
        ];

        $email_template_data = [];
        $email_template_service_data = $this->emailTemplateService->getEmailTemplateData($request->user()->id);
        $total_email_template_records = count($email_template_service_data);
        $counter = 1;

        foreach($email_template_service_data as $key => $template )
        {
            $days_diff = $counter == $total_email_template_records ? true : false;
            $email_template_data[$key] = $template;
            $email_template_data[$key]['invoice_data'] = $this->collectionInvoiceService->getTemplateInvoiceCollections(true, $per_page, $sort, $filters, $template['id'], $days_diff);
            $counter++;
        }

        if( $counter > 1 )
        {
            //further table query
            $email_template_data[$counter-1]['get_templates']['name'] = "Further Action";
            $email_template_data[$counter-1]['invoice_data'] = $this->collectionInvoiceService->getTemplateInvoiceCollections(true, $per_page, $sort, $filters, 0, true);
        }

        $action = false;
        if ($request->session()->exists('action')) {
            $action = true;
        }

        // dump($email_template_data);

        // $result = $this->collectionInvoiceService->getAll(true, $per_page, $sort, $filters);
        // dump($result);
        return Inertia::render('CollectionInvoice/List', [
            // 'collection_invoices' => $result,
            'filters' => $request->only(['search']),
            'per_page' => $per_page,
            'email_template_data' => $email_template_data,
            'legal_message_1' => config("common.legal_message_1"),
            'legal_message_2' => config("common.legal_message_2"),
            'legal_message_3' => config("common.legal_message_3"),
            'action' => $action,
            'phone_action_button_email' => config("common.phone_action_button_email"),
            'post_action_button_email' => config("common.post_action_button_email"),
            'legal_action_button_email' => config("common.legal_action_button_email")
        ]);

    }

    public function legalAction(Request $request): RedirectResponse
    {
        // dd($request->all());

        $request->validate([
            'contact_id' => 'required|string|max:50',
            'legal_message_key' => 'required|string',
            'email' => 'required|email'
        ]);

        $contact = $this->contactService->findByRefContactID($request->contact_id);
        $template_id = $this->customHelper->parseLastEmailTemplateID($contact['template_id']);
        $template = $this->emailTemplateService->getEmailTemplateData(0, $template_id);
        $invoices = $this->collectionInvoiceService->getCollectionInvoicesByContactID($request->contact_id, true);

        if( count($invoices) > 0 )
            $xero = new Xero2Api($request->user());

        $email_data['subject'] = config("common.legal_action_subject");
        $email_data['template'] = $request->legal_message_key;
        $email_data['reference'] = $contact['ref_contact_id'];
        $email_data['current_date'] = Carbon::now()->format('l jS F Y');
        $email_data['contact_name'] = $contact['name'];
        $email_data['contact_email'] = $request->email;
        $email_data['invoice_due_name_from'] = "areebiqbal0@gmail.com";
        $email_data['last_email'] = $contact['last_email'];
        $email_data['due_date'] = Carbon::now()->addDays(7)->format('Y-m-d');
        $email_data['total_demand_payment'] = 0;

        $email_table_data = [];
        $email_pdf_files = [];

        //activity id to log the id
        if( $request->email == config("common.phone_action_button_email") )
            $activity_id = 3;
        elseif( $request->email == config("common.post_action_button_email") )
            $activity_id = 4;
        else
            $activity_id = 5;

        foreach($invoices as $k => $val)
        {
            $email_table_data[$k]['invoice_number'] = $val['invoice_number'];
            $email_table_data[$k]['invoice_date'] = $val['invoice_date'];
            $email_table_data[$k]['due_date'] = $val['due_date'];
            $email_table_data[$k]['amount_due'] = $val['amount_due'];
            $email_data['total_demand_payment'] += $val['amount_due'];


            $pdfInvoice = $xero->getInvoiceAsPdf($val['invoice_id']);
            File::copy($pdfInvoice['data']->getpathName(), public_path("files/{$val['invoice_number']}.pdf"));
            $email_pdf_files[$k] = $val['invoice_number'].".pdf";

            //activity data to log activity
            $activity_data[] = [
                'activity_id' => $activity_id,
                'invoice_id' => $val['id'],
                'email_template_id' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        $docx = new TemplateProcessor(public_path("files/".$template[0]['get_templates']['document']));
        $docx->setValues($email_data);
        $docx->cloneRowAndSetValues('invoice_number',$email_table_data);
        $path = public_path(config("common.email_attatchment_path"));
        $fileName = $email_data['contact_email'].time().'.docx';
        $docx->saveAs($path . $fileName);

        $email_data['file_name'] = $fileName;

        InvoiceEmailJob::dispatch($email_data, $email_pdf_files);
        $this->userActivityService->bulkInsert($activity_data);

        $request->session()->flash('action', 'Further Action Taken Successfully!');
        return redirect('collection_invoice');

    }

    public function store(Request $request)
    {
        $data = $request->all();
        if( !empty($data) )
        {
            $result = [];
            $contacts = [];

            foreach ($data as $v){
                // $already_exist = CollectionInvoice::whereLike('invoice_id', $v['invoice_id'])
                //     ->get()->toArray();

                // if( count($already_exist) > 0 )
                    // continue;
                // $contacts[] = $v['contact_id'];

                $result = [
                    "user_id" => $request->user()->id,
                    "invoice_id" => $v['invoice_id'],
                    "contact_id" => $v['contact_id'],
                    "contact_name" => $v['contact_name'],
                    "contact_email" => $v['contact_email']." ",
                    "invoice_number" => $v['invoice_number'],
                    "reference" => $v['reference'],
                    "amount_due" => $v['amount_due'],
                    "amount_paid" => $v['amount_paid'],
                    "import_amount_paid" => $v['amount_paid'],
                    "invoice_date" => Carbon::parse($v['date'])->format("Y-m-d"),
                    "due_date" => Carbon::parse($v['due_date'])->format("Y-m-d"),
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ];

                $this->collectionInvoiceService->deleteCollectionViaInvoiceID($v['invoice_id']);
                $this->collectionInvoiceService->save($result);

            }

            // try{

                // if(count($result) > 0)
                    // $this->collectionInvoiceService->bulkInsert($result);
                    // CollectionInvoice::insert($result);
            // }catch(Exception $e){

            // }

        }
    }


    public function history(Request $request)
    {
        $data = $this->userActivityService->getInvoiceHistory($request->id);
        return $data;
    }


    public function sentDueEmail(Request $request)
    {
        $data = CollectionInvoice::getUserDueInvoice($request->user()->id);

        foreach($data as $val){
            try{
                $email_data = [
                    'subject' => config("common.email_due_invoice_subject"),
                    'template' => config("common.email_due_invoice_template"),
                    'reference' => $val['reference'],
                    'current_date' => date("l jS F Y"),
                    'contact_name' => $val['contact_name'],
                    'contact_email' => $val['contact_email'],
                    'invoice_due_name_from' => config("common.email_from"),
                    'invoice_number' => $val['invoice_number'],
                    'invoice_date' => $val['invoice_date'],
                    'due_date' => $val['due_date'],
                    'amount_due' => $val['amount_due'],
                    'due_date' => $val['due_date']
                ];

                $pdf = PDF::loadView('email_templates.letter3', $email_data);
                $path = public_path('files/');
                $fileName = 'letter'.time().'.pdf';

                $pdf->save($path . $fileName);
                $email_data['pdf_file'] = $fileName;
                InvoiceEmailJob::dispatch($email_data);
                die("Due Invoices email sent successfully.");
            }catch(Exception $e){
                echo 'Message: ' .$e->getMessage();
            }
        }
    }
}
