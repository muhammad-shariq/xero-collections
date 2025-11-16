<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\RoleService;
use Inertia\Inertia;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    private $userService;
    private $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        $per_page = $request->input('per_page')??10;
        $sort_by = $request->input('sort_by')??'id';
        $sort_dir = $request->input('sort_dir')??'desc';
        $filters = [
            'keyword' => $request->input('search')
        ];
        $sort = [
            'field' => $sort_by,
            'direction' => $sort_dir
        ];

        $result = $this->userService->getAll(true, $per_page, $sort, $filters);

        return Inertia::render('User/List', [
            'user_data' => $result,
            'filters' => $request->only(['search']),
            'per_page' => $per_page
        ]);
    }

    public function add(Request $request)
    {
        $roles = $this->roleService->getAll();
        return Inertia::render('User/Add', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'roles' => $roles
        ]);
    }

    public function edit(Request $request)
    {
        $roles = $this->roleService->getAll();
        $user_data = $this->userService->findById($request->id, true);

        return Inertia::render('User/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'roles' => $roles,
            'user_data' => $user_data
        ]);
    }

    public function login(Request $request)
    {
        $this->userService->login($request->id, $request->user()->id);
        return redirect('dashboard');
    }

    public function adminBack(Request $request)
    {
        $this->userService->adminBack($request);
        return redirect('dashboard');
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->save($request);
    }

    public function update(StoreUserRequest $request)
    {
        $this->userService->update($request);
    }

    public function destroy(Request $request)
    {
        $this->userService->delete($request->id);
    }
}
