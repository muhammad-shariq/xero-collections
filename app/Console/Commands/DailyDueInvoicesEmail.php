<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\InvoiceEmailJob;
use PDF;
use Exception;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use File;
use App\Common\Xero2Api;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailTemplateService;
use App\Services\ContactService;
use App\Services\UserActivityService;

class DailyDueInvoicesEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-due-invoices-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent an email for due invoices after certain time set in the database';

    private $emailTemplateService;
    private $contactService;
    private $userActivityService;

    public function __construct(EmailTemplateService $emailTemplateService, ContactService $contactService, UserActivityService $userActivityService)
    {
        parent::__construct();
        $this->emailTemplateService = $emailTemplateService;
        $this->contactService = $contactService;
        $this->userActivityService = $userActivityService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //get email templates data for sending
        $email_template = $this->emailTemplateService->getEmailTemplateData();

        foreach($email_template as $days){
            //get pending invoices data
            $data = $this->contactService->getUserDueInvoice($days);
            // dump($data);
            // continue;
            /**
             * Get invoices from xero portal download as a pdf
             * and part of the email as an attatchment
            */

            //xero authentication via id
            if( count($data) > 0 )
                $xero = new Xero2Api(Auth::loginUsingId($days['user_id']));

            foreach($data as $e => $key_numbers){

                $email_data = [
                    'total_demand_payment' => 0
                ];
                $email_table_data = [];
                $email_pdf_files = [];

                $activity_data = [];

                try{
                    //sending email data
                    foreach($key_numbers as $k => $val){
                        $email_data['subject'] = $days['get_templates']['email_subject'];
                        $email_data['template'] = $days['get_templates']['email_message'];
                        $email_data['reference'] = $val['reference'];
                        $email_data['current_date'] = Carbon::now()->format('l jS F Y');
                        $email_data['contact_name'] = $val['contact_name'];
                        $email_data['contact_email'] = $val['email'];
                        $email_data['invoice_due_name_from'] = $days['get_templates']['email_name'];
                        $email_data['last_email'] = $val['last_email'];
                        $email_data['due_date'] = Carbon::now()->addDays(7)->format('Y-m-d');
                        $email_table_data[$k]['invoice_number'] = $val['invoice_number'];
                        $email_table_data[$k]['invoice_date'] = $val['invoice_date'];
                        $email_table_data[$k]['due_date'] = $val['due_date'];
                        $email_table_data[$k]['amount_due'] = $val['amount_due'];
                        $email_data['total_demand_payment'] += $val['amount_due'];
                        $email_data['cont_id'] = $val['cont_id'];
                        $template_id = $val['template_id'];

                        //downloading invoices as a pdf and store
                        $pdfInvoice = $xero->getInvoiceAsPdf($val['invoice_id']);
                        File::copy($pdfInvoice['data']->getpathName(), public_path("files/{$val['invoice_number']}.pdf"));
                        $email_pdf_files[$k] = $val['invoice_number'].".pdf";

                        //getting activity for inserting invoice data
                        $activity_data[] = [
                            'activity_id' => 2,
                            'invoice_id' => $val['id'],
                            'email_template_id' => $days['id'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }

                    //updating word docx
                    $docx = new TemplateProcessor(public_path("files/".$days['get_templates']['document']));
                    $docx->setValues($email_data);
                    $docx->cloneRowAndSetValues('invoice_number',$email_table_data);
                    $path = public_path(config("common.email_attatchment_path"));
                    $fileName = $email_data['contact_email'].time().'.docx';
                    $docx->saveAs($path . $fileName);

                    //For pdf handling Leave as it is as it can be used in future for handling pdf data
                    // $pdf = PDF::loadView('email_templates.'.$days['get_templates']['name'], $email_data);
                    // $path = public_path(config("common.email_attatchment_path"));
                    // $fileName = $email_data['contact_email'].time().'.pdf';
                    // $pdf->save($path . $fileName);

                    $email_data['file_name'] = $fileName;
                    InvoiceEmailJob::dispatch($email_data, $email_pdf_files);

                    $update = [
                        'last_email' => Carbon::now(),
                        'template_id' => $template_id.",--".$days['id']."--"
                    ];
                    $this->contactService->partial_update($email_data['cont_id'], $update);

                    $this->userActivityService->bulkInsert($activity_data);

                }catch(Exception $e){
                    echo 'Message: ' .$e->getMessage();
                }
            }
        }

        die("Due Invoices email sent successfully.");
    }


}
