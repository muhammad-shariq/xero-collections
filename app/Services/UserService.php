<?php

namespace App\Services;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserService{

    private $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function getAll($pagination = false){
        if($pagination){
            return $this->userRepository->paginate();
        }
        return $this->userRepository->all();
    }

    public function findById(int $id, bool $array = false){
        $data = $this->userRepository->find($id);
        if( $array )
            $data = $data->toArray();
        return $data;
    }

    public function save(StoreUserRequest $request){

        $validated_data = $request->safe()->only(['name', 'email', 'password', 'role_id']);

        $result = [
            'name' => $validated_data['name'],
            'email' => $validated_data['email'],
            'password' => Hash::make(trim($validated_data['password'])),
            'email_verified_at' => Carbon::now(),
            'role_id' => $validated_data['role_id'],
            'parent_id' => $request->user()->role_id == 1 ? $request->user()->id : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'remember_token' => Str::random(10),
        ];

        return $this->userRepository->create($result);
    }

    public function getAllOrganizations(): array
    {
        return $this->userRepository->getAllOrganizations();
    }

    public function update(StoreUserRequest $request)
    {
        $validated_data = $request->safe()->only(['name', 'email', 'password', 'role_id']);

        $update = [
            'name' => $validated_data['name'],
            'email' => $validated_data['email'],
            'password' => Hash::make(trim($validated_data['password'])),
            'role_id' => $validated_data['role_id']
        ];
        return $this->userRepository->update($request->id, $update);
    }

    public function delete(int $id)
    {
        $this->userRepository->delete($id);
    }

    public function login(int $user_id, int $admin_id): User
    {
        session(['is_admin_login' => true, "admin_login_id" => $admin_id]);
        return Auth::loginUsingId($user_id, true);
    }

    public function adminBack(Request $request)
    {
        $admin_id = session('admin_login_id');
        $request->session()->forget(['is_admin_login', 'admin_login_id']);
        return Auth::loginUsingId($admin_id, true);
    }

    public function findExistEmailValidate(string $email, int $id) :array
    {
        return $this->userRepository->findExistEmailValidate($email, $id)->toArray();
    }

}
