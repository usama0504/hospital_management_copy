<?php

namespace App\Providers;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\SandboxGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Online payment gateway: config/payments.php ke 'driver' se chunta hai.
        // Asli gateway add karne ke liye uski class yahan match mein jodein.
        $this->app->bind(PaymentGateway::class, function () {
            return match (config('payments.driver')) {
                'sandbox' => new SandboxGateway(),
                default => throw new \RuntimeException('Unknown payment gateway: ' . config('payments.driver')),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
