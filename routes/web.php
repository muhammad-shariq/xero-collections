<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\XeroController;
use App\Http\Controllers\CollectionInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\TemplateController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('login');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
});

// Route::get('/dashboard', function () {
//     // return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth', 'verified'])->group(function (){
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });

Route::middleware('auth')->group(function () {

    Route::middleware('verified')->group(function (){
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('users')->group(function (){

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/add', [UserController::class, 'add'])->name('users.add');

        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::middleware('admin')->group(function (){
            Route::get('/users/login/{id}', [UserController::class, 'login'])->name('users.login');
        });

    });

    Route::middleware('staff')->group(function (){

        Route::get('/template', [TemplateController::class, 'index'])->name('template');
        Route::get('/template/edit/{id}', [TemplateController::class, 'edit'])->name('template.edit');
        Route::get('/template/add', [TemplateController::class, 'add'])->name('temp.add');
        Route::post('/template', [TemplateController::class, 'store'])->name('temp.store');
        Route::post('/template/update', [TemplateController::class, 'update'])->name('temp.update');
        Route::delete('/template/{id}', [TemplateController::class, 'destroy'])->name('template.destroy');

    });

    Route::get('/users/admin-back/{id}', [UserController::class, 'adminBack'])->name('users.admin.back');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/xero/authorise', [XeroController::class, 'authorise'])->name('xero.authorise');
    Route::get('/xero/callback', [XeroController::class, 'callback'])->name('xero.callback');

    Route::get('/xero/invoice', [XeroController::class, 'getInvoices'])->name('xero.invoices');
    Route::get('/xero/invoice/data', [XeroController::class, 'getInvoiceData'])->name('xero.invoice.data');

    Route::get('/collection_invoice', [CollectionInvoiceController::class, 'index'])->name('collection_invoice');
    Route::get('/send-legal-action/{contact_id}', [CollectionInvoiceController::class, 'legalAction'])->name('collection_invoice.legal.action');
    Route::post('/send-legal-action', [CollectionInvoiceController::class, 'legalAction'])->name('collection_invoice.post.legal.action');
    Route::post('/collection_invoice', [CollectionInvoiceController::class, 'store'])->name('collection_invoice.store');
    Route::get('/sent_invoice_due_email', [CollectionInvoiceController::class, 'sentDueEmail'])->name('collection_invoice.due_email');
    Route::get('/get-invoice-history/{id}', [CollectionInvoiceController::class, 'history'])->name('collection_invoice.history');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::get('/sync-contacts', [ContactController::class, 'sync'])->name('sync');
    Route::get('/contact/edit/{id}', [ContactController::class, 'edit'])->name('contact.edit');
    Route::patch('/contact', [ContactController::class, 'update'])->name('contact.update');


    Route::get('/email-template', [EmailTemplateController::class, 'index'])->name('email.template');
    Route::get('/email-template/add', [EmailTemplateController::class, 'add'])->name('template.add');
    Route::get('/email-template/edit/{id}', [EmailTemplateController::class, 'edit'])->name('scheduled.template.edit');
    Route::post('/email-template', [EmailTemplateController::class, 'store'])->name('template.store');
    Route::patch('/email-template', [EmailTemplateController::class, 'update'])->name('scheduled.template.update');
    Route::delete('/email-template/{id}', [EmailTemplateController::class, 'destroy'])->name('scheduled.template.destroy');

    // Route::get('/template/add', [EmailTemplateController::class, 'add'])->name('template.add');

});

// Route::get('/email', [EmailController::class, 'sent'])->name('email');
Route::post('/xero-webhook', [XeroController::class, 'webhook'])->name('xero.webhook');

require __DIR__.'/auth.php';
// require __DIR__.'/api.php';
