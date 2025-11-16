<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Observers\UserObserver;
use App\Observers\CollectionInvoiceObserver;
use App\Models\CollectionInvoice;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class); //register user observer
        CollectionInvoice::observe(CollectionInvoiceObserver::class); //register collection_invoice observer
        Route::pattern('id', '[0-9]+'); //added validation rule on id globally at Route level
    }
}
