<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class InvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $pdf_files;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, array $pdf_files)
    {
        $this->data = $data;
        $this->pdf_files = $pdf_files;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->data['contact_email'])->send(new InvoiceEmail($this->data, $this->pdf_files));
        // \Log::info(public_path(config("common.email_attatchment_path")) . $this->data['pdf_file']);
        File::delete(public_path(config("common.email_attatchment_path")) . $this->data['file_name']);
        foreach($this->pdf_files as $pdf)
        {
            File::delete(public_path(config("common.email_attatchment_path")) . $pdf);
        }
    }
}
