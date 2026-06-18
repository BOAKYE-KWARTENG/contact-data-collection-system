<?php

namespace App\Providers;


use App\Models\Contact;
use App\Observers\ContactObserver;
use Illuminate\Support\ServiceProvider;



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
        // This binds the observer globally to the contact execution lifecycle
        Contact::observe(ContactObserver::class);
    }


    
}
