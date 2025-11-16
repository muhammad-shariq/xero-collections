<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ContactCreateRequest;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Services\ContactService;
use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StoreContactRequest;

class ContactController extends Controller
{
    private $contactRepository;
    private $contactService;

    public function __construct(
        ContactService $contactService,
        ContactRepository $contactRepository
    ){
        $this->contactService = $contactService;
        $this->contactRepository = $contactRepository;
    }

    public function index(Request $request)
    {
        $sync = false;
        if ($request->session()->exists('sync')) {
            $sync = true;
        }

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

        $result = $this->contactService->getAll(true, $per_page, $sort, $filters);

        return Inertia::render('Contact/List', [
            'contacts' => $result,
            'filters' => $request->only(['search']),
            'per_page' => $per_page,
            'sync' => $sync
        ]);
    }

    public function edit(Request $request)
    {
        $data = $this->contactService->findById($request->id, true);
        // dd($data);
        $error = false;
        if ($request->session()->exists('error')){
            $error = true;
        }

        return Inertia::render('Contact/Edit', [
            'data' => $data,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'error' => $error
        ]);
    }

    public function update(StoreContactRequest $request)
    {
        $update = $this->contactService->updateXeroContact($request);
        if(!$update)
            $request->session()->flash('error', 'Data not updated');
    }

    public function sync(Request $request)
    {
        Artisan::call("app:add-xero-contacts {$request->user()->id}");
        $request->session()->flash('sync', 'Contacts Sync successful!');
        return redirect('contact');
    }
}
