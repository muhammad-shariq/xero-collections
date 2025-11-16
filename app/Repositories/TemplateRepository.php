<?php
namespace App\Repositories;

use App\Models\Template;
use App\Interfaces\TemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TemplateRepository implements TemplateRepositoryInterface
{
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function all($default = false): Collection|array
    {
        $query = Template::all();
        if($default)
            $query = $query->where('default', 1);

        return $query->toArray();
    }

    public function find(int $id): Template
    {
        return Template::findOrFail($id);
    }

    public function findByUserID(int $user_id): Collection
    {
        return Template::where('user_id', $user_id)->get();
        // return $this->template->where('user_id', $user_id)->first();
    }

    public function findByTemplateName(string $template_name): ?Template
    {
        return $this->template->where('name', $template_name);
    }

    public function create(array $data): Template
    {
        return Template::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Template::where('id', $id)->update($data);
        // $template = $this->find($id);
        // $template->update($data);
        // return $template;
    }

    public function delete(int $id): void
    {
        Template::destroy($id);
    }

    public function paginate(int $per_page = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->template->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);
        //$query->where('user_id', Auth::id())

        return $query->select('templates.*','users.name as user_name')
                ->join('users', 'users.id', 'templates.user_id')
                ->paginate($per_page)->withQueryString()->through(fn($ci) => [
            'id' => $ci->id,
            'template_name' => $ci->name,
            'created_by' => $ci->user_name,
            'subject' => $ci->email_subject,
            'email_from' => $ci->email_from,
            'email_from_name' => $ci->email_name,
            'updated_at' => $ci->updated_at->toDateTimeString(),
            'created_at' => $ci->created_at->toDateTimeString(),
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['keyword'])) {
            $query->where('user_id', 'like', "%{$filters['keyword']}%");
            $query->orWhere('name', 'like', "%{$filters['keyword']}%");
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
