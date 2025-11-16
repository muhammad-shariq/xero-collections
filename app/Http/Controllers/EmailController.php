<?php

namespace App\Http\Controllers;
use App\Mail\InvoiceEmail;
use Illuminate\Http\Request;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\PhpWord;
use App\Models\CollectionInvoice;
use PDF;
use ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;

class EmailController extends Controller
{
    //
    public function sent(Request $request)
    {
        $template = new TemplateProcessor(public_path('files\letter_1.docx'));
        // echo public_path('files\letter_1.docx');

        $email_data['email'] = "areebiqbal0@gmail.com";
        $email_data['address'] = "Karachi, sindh Pakistan.";
        $email_table_data[0]['invoice_number'] = "Invoice Number";
        $email_table_data[0]['invoice_date'] = "Invoice Date";
        $email_table_data[0]['due_date'] = "Due Date";
        $email_table_data[0]['amount_due'] = "Amount Due";

        $email_table_data[1]['invoice_number'] = "Invoice Number";
        $email_table_data[1]['invoice_date'] = "Invoice Date";
        $email_table_data[1]['due_date'] = "Due Date";
        $email_table_data[1]['amount_due'] = "Amount Due";

        $email_table_data[2]['invoice_number'] = "Invoice Number";
        $email_table_data[2]['invoice_date'] = "Invoice Date";
        $email_table_data[2]['due_date'] = "Due Date";
        $email_table_data[2]['amount_due'] = "Amount Due";
        // $template->setValue('email', 'areebiqbal0@gmail.com');
        // $template->setValue('address', 'Karachi, sindh Pakistan.');
        $template->setValues($email_data);
        $template->cloneRowAndSetValues('invoice_number',$email_table_data);

        $template->saveAs(public_path('files\letter_result.docx'));
        die("__");

        $full_path = public_path('files\letter_1.docx');
        $rand_no = rand(111111, 999999);
        $fileName = "results_" . $rand_no . ".docx";

        $folder   = public_path('files');
        $full_path = $folder . '/' . $fileName;
        //Copy the Template file to the Result Directory
        // copy($template_file_name, $full_path);

        // add calss Zip Archive
        $zip_val = new ZipArchive;

        //Docx file is nothing but a zip file. Open this Zip File
        if($zip_val->open($full_path) == true)
        {
            // In the Open XML Wordprocessing format content is stored.
            // In the document.xml file located in the word directory.

            $key_file_name = 'word/document.xml';
            $message = $zip_val->getFromName($key_file_name);

            $timestamp = date('d-M-Y H:i:s');

            // this data Replace the placeholders with actual values
            $message = str_replace("{officeaddress}", "onlinecode org", $message);
            $message = str_replace("{Ename}", "ingo@onlinecode.org", $message);
            $message = str_replace("{name}", "www.onlinecode.org", $message);

            //Replace the content with the new content created above.
            $zip_val->addFromString($key_file_name, $message);
            $zip_val->close();
        }
        die("@");
        $docx = new \IRebega\DocxReplacer\Docx(public_path('\files\letter_1.docx'));
        // echo public_path('files/letter_1.docx');
        $docx->replaceText("[email]", "replace_email");
        die();
        ini_set('memory_limit', '-1');
        $data = CollectionInvoice::getUserDueInvoice($request->user()->id);

        foreach($data as $val){

            $data = [
                'title' => 'Report Title',
                'content' => 'Report Content',
            ];

            Mail::to('areeb_in_pakistan@hotmail.com')->send(new InvoiceEmail($data));
            // $pdf = PDF::loadView($email_data['template'], $email_data);
            // return $pdf->download('test.pdf');
            // Mail::to($email_data['contact_email'])->send(new InvoiceEmail($pdf));
            die("Due Invoices email sent successfully.");

        }
        Mail::to($data['contact_email'])->send(new InvoiceEmail($data));
        // $pdf = PDF::loadView($email_data['template'], $email_data);
        // return $pdf->download('test.pdf');
        // print_r($pdf);
        die("@");
        // $phpWord = \PhpOffice\PhpWord\IOFactory::load(public_path('files/letter_1.docx'));
        // $htmlContent = strip_tags($phpWord->getValue());
        // echo $htmlContent;

        // $parser = new \Smalot\PdfParser\Parser();
        // $pdf = $parser->parseFile(public_path('files/letter_1.pdf'));
        // $text = $pdf->getText();
        // echo $text;
        die("DD");
        // $pdfFile = public_path('files/sample.pdf');
        // die("check email");
        // Mail::to('areeb@nextgeni.com')->send(new MyTestEmail("areeb"));
        // Mail::to("areeb@nextgeni.com")->send(new InvoiceEmail());

        // die("Email sent successfully Done via SendGrid!!.");
    }
}
