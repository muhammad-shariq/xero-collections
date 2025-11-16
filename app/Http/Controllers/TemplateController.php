<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\TemplateService;
use Inertia\Response;
use App\Http\Requests\StoreTemplateRequest;

class TemplateController extends Controller
{
    private $templateService;

    public function __construct( TemplateService $templateService )
    {
        $this->templateService = $templateService;
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

        $result = $this->templateService->getAll(true, $per_page, $sort, $filters);

        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        // die();

        return Inertia::render('Template/List', [
            'template' => $result,
            'filters' => $request->only(['search']),
            'per_page' => $per_page
        ]);
    }

    public function add(Request $request) :Response
    {
        return Inertia::render('Template/Add', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function edit(Request $request)
    {
        $data = $this->templateService->findById($request->route('id'), true);

        return Inertia::render('Template/Edit', [
            'data' => $data,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function store(StoreTemplateRequest $request)
    {
        $this->templateService->save($request);
    }

    public function update(StoreTemplateRequest $request)
    {
        $this->templateService->update($request);
    }

    public function destroy(Request $request)
    {
        $this->templateService->delete($request->id);
    }
}
