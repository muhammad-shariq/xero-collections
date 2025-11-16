<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\TemplateService;
use App\Services\EmailTemplateService;
use Carbon\Carbon;

class UserObserver
{
    private $templateService;
    private $emailTemplateService;

    public function __construct(TemplateService $templateService, EmailTemplateService $emailTemplateService )
    {
        $this->templateService = $templateService;
        $this->emailTemplateService = $emailTemplateService;
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $defaultTemplates = $this->templateService->getAll(false, 0 ,[], [], true);

        foreach($defaultTemplates as $k => $template)
        {
            //bulkInsert
            $email_template[$k]['user_id'] = $user->id;
            $email_template[$k]['template_id'] = $template['id'];
            $email_template[$k]['trigger_days'] = rand(1,10);
            $email_template[$k]['created_at'] = $email_template[$k]['updated_at'] = Carbon::now();
        }
        $this->emailTemplateService->bulkInsert($email_template);
        // Log::info($defaultTemplates);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
