<?php
namespace App\Repositories;

use App\Models\Role;
use App\Interfaces\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RoleRepository implements RoleRepositoryInterface
{
    private $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function all(): Collection|array
    {
        // dd(Auth::user()->role_id);
        if( Auth::user()->role_id == 1 )
            return Role::all()->where('id','!=',Auth::user()->role_id)->toArray();

        return Role::all()->where('id', 1)->toArray();
    }

    public function paginate(int $per_page = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->role->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);

        return $query->paginate($per_page)->withQueryString()->through(fn($ci) => [
            'id' => $ci->id,
            'role_name' => $ci->role_name,
            'role_slug' => $ci->role_slug,
            'updated_at' => $ci->updated_at->toDateTimeString(),
            'created_at' => $ci->created_at->toDateTimeString(),
        ]);
        // return $query->paginate($perPage);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['keyword'])) {
            $query->where('role_name', 'like', "%{$filters['keyword']}%");
            $query->orWhere('role_slug', 'like', "%{$filters['keyword']}%");
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
