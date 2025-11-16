<?php
namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function all(): Collection
    {
        return $this->user->all();
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    public function findExistEmailValidate(string $email, int $id):Collection
    {
        return User::where([
            ['email', '=', $email],
            ['id' , '!=' , $id]
        ])->get();
    }

    public function findByName(string $name): ?User
    {
        return $this->user->where('name', $name)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        User::destroy($id);
    }

    public function getAllOrganizations(): Collection|array
    {
        return User::where('role_id', 1)
            ->whereNotNull('xero_organization_name')
            ->get()->toArray();
    }

    public function paginate(int $perPage = 15, array $sort = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->user->newQuery();

        // Apply filters (adapt based on your needs)
        $this->applyFilters($query, $filters);

        // Apply sorting (adapt based on your sortable fields)
        $this->applySorting($query, $sort);
        $query = $query->select('roles.role_name', 'users.*')->join('roles', 'users.role_id', '=', 'roles.id');
        if( Auth::user()->role_id == 1 )
        {
            $query = $query->where('parent_id', Auth::id());
        }else{
            $query = $query->where('role_id', 1);
        }

        return $query->paginate($perPage)->withQueryString()->through(fn($ci) => [
            'id' => $ci->id,
            'email' => $ci->email,
            'name' => $ci->name,
            'role_name' => $ci->role_name,
            'updated_at' => $ci->updated_at->toDateTimeString(),
            'created_at' => $ci->created_at->toDateTimeString(),
        ]);
        // return $query->paginate($perPage);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        // Example filter: searching by name
        if (isset($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', "%{$filters['email']}%");
        }

        if (isset($filters['role_name'])) {
            $query->where('role_name', 'like', "%{$filters['role_name']}%");
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
        if (count($sort) > 1) {
            $sortSql = '';
            foreach ($sort as $field => $direction) {
                $sortSql .= "{$field} {$direction}, ";
            }
            $sortSql = rtrim($sortSql, ', ');
            $query->orderByRaw($sortSql);
        }
    }
}
