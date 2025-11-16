<?php

namespace App\Repositories;
use App\Models\UserActivity;
use App\Interfaces\UserActivityRepositoryInterface;

class UserActivityRepository implements UserActivityRepositoryInterface
{
    private $userActivityModel;
    /**
     * Create a new class instance.
     */
    public function __construct(UserActivity $userActivityModel)
    {
        $this->userActivityModel = $userActivityModel;
    }

    public function insert(array $data): UserActivity
    {
        return UserActivity::create($data);
    }

    public function bulkInsert(array $data): bool
    {
        return UserActivity::insert($data);
    }

    public function getInvoiceHistory(int $invoice_id): array
    {
        return UserActivity::select('activities.activity_name','activities.activity_message','user_activities.invoice_id','user_activities.created_at','templates.name')
                    ->join('activities', 'activities.id', 'user_activities.activity_id')
                    ->leftJoin('email_templates','email_templates.id','user_activities.email_template_id')
                    ->leftJoin('templates','templates.id','email_templates.template_id')
                    ->where('invoice_id',$invoice_id)
                    ->get()->toArray();
    }

}
