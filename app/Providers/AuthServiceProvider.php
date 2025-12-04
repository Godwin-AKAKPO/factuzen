<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Invoice;
use App\Policies\ClientPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting():void{
       RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip()); //Générer une clé unique basé sur l'adresse IP de l'appareil et du mail
            return Limit::perMinute(5)->by($throttleKey); //On définit une limite de 5 tentative par minute 
        });
    }
}
