<?php
namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Interfaces\EmailTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    protected $emailTemplate;

    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function all(): Collection
    {
        return EmailTemplate::all();
    }

    public function find(int $id): EmailTemplate
    {
        return EmailTemplate::findOrFail($id);
    }

    public function findByTemplateID(string $template_id): ?EmailTemplate
    {
        return $this->emailTemplate->where('template_id', $template_id)->first();
    }

    public function findByTriggerDays(string $trigger_days): ?EmailTemplate
    {
        return $this->emailTemplate->where('trigger_days', $trigger_days)->first();
    }

    public function bulkInsert(array $data): bool
    {
        return EmailTemplate::insert($data);
    }

    public function create(array $data): EmailTemplate
    {
        return EmailTemplate::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return EmailTemplate::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        EmailTemplate::destroy($id);
    }

    public function getEmailTemplateData(int $user = 0, int $email_template = 0): array
    {
       return EmailTemplate::getEmailTemplateData($user, $email_template);
    }

    public function paginate(int $per_page = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->emailTemplate->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);

        return $query->select('templates.name', 'email_templates.*', 'users.xero_organization_name')
            ->join('templates', 'templates.id', '=', 'email_templates.template_id')
            ->join('users', 'users.id', '=', 'email_templates.user_id')
            // ->where('email_templates.user_id', Auth::id())
            ->paginate($per_page)->withQueryString()->through(fn($ci) => [
            'id' => $ci->id,
            'user_id' => $ci->user_id,
            'xero_organization_name' => $ci->xero_organization_name,
            'template_id' => $ci->template_id,
            'name' => $ci->name,
            'trigger_days' => $ci->trigger_days,
            'updated_at' => $ci->updated_at->toDateTimeString(),
            'created_at' => $ci->created_at->toDateTimeString(),
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['keyword'])) {
            $query->where('user_id', 'like', "%{$filters['keyword']}%");
            $query->orWhere('template_id', 'like', "%{$filters['keyword']}%");
            $query->orWhere('trigger_days', 'like', "%{$filters['keyword']}%");
        }
        // Add more filters as needed
    }

    protected function applySorting(Builder $query, array $sort): void
    {
        if (empty($sort)) {
            return;
        }

        $sortField = $sort['field'] ?? 'id'; // Default to 'id' if not specified
        $sortDirection = $sort['direction'] ?? 'asc'; // Default to ascending

        $query->orderBy($sortField, $sortDirection);

        // Handle complex sorting with multiple columns using DB::raw (optional)
        // if (count($sort) > 1) {
        //     $sortSql = '';
        //     foreach ($sort as $field => $direction) {
        //         $sortSql .= "{$field} {$direction}, ";
        //     }
        //     $sortSql = rtrim($sortSql, ', ');
        //     $query->orderByRaw($sortSql);
        // }
    }
}
