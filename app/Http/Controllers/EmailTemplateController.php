<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\EmailTemplateService;
use App\Services\TemplateService;
use Inertia\Response;
use App\Http\Requests\StoreEmailTemplateRequest;
use App\Services\UserService;

class EmailTemplateController extends Controller
{
    private $templateService;
    private $emailTemplateService;
    private $userService;

    public function __construct(
        EmailTemplateService $emailTemplateService,
        TemplateService $templateService,
        UserService $userService
    ){
        $this->emailTemplateService = $emailTemplateService;
        $this->templateService = $templateService;
        $this->userService = $userService;
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

        $result = $this->emailTemplateService->getAll(true, $per_page, $sort, $filters);


        return Inertia::render('EmailTemplate/List', [
            'email_template' => $result,
            'filters' => $request->only(['search']),
            'per_page' => $per_page
        ]);
    }

    public function add(Request $request) :Response
    {
        $templates = $this->templateService->findByUserID($request->user()->id);
        $organizations = $this->userService->getAllOrganizations();

        return Inertia::render('EmailTemplate/Add', [
            'templates' => $templates,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'organizations' => $organizations
        ]);
    }

    public function store(StoreEmailTemplateRequest $request)
    {
        $this->emailTemplateService->save($request);
    }

    public function edit(Request $request)
    {
        $data = $this->emailTemplateService->findById($request->route('id'), true);
        $templates = $this->templateService->findByUserID($request->user()->id);
        $organizations = $this->userService->getAllOrganizations();

        return Inertia::render('EmailTemplate/Edit', [
            'templates' => $templates,
            'data' => $data,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'organizations' => $organizations,
        ]);
    }

    public function update(StoreEmailTemplateRequest $request)
    {
        $this->emailTemplateService->update($request);
    }

    public function destroy(Request $request)
    {
        $this->emailTemplateService->delete($request->id);
    }
}
