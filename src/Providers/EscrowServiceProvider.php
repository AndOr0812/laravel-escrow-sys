<?php

namespace VSoft\LaravelEscrow\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use VSoft\LaravelCurrencies\CurrenciesServiceProvider;
use VSoft\LaravelEscrow\Adapters\Stripe\StripePaymentGateway;
use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract;
use VSoft\LaravelEscrow\Contracts\SalesAccountContract;
use VSoft\LaravelEscrow\Events\RefundCreated;
use VSoft\LaravelEscrow\Jobs\CreateReversedTransaction;
use VSoft\LaravelEscrow\TransactionTypes\AccountPayout;
use VSoft\LaravelEscrow\SalesAccount;
use VSoft\QueryKit\QueryKitServiceProvider;

class EscrowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateEscrowsTable')) {
            $this->publishes([
                __DIR__.'/../../database/migrations/create_escrows_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_escrows_table.php'),
                __DIR__.'/../../database/migrations/create_escrow_transactions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_escrow_transactions_table.php'),
            ], 'migrations');
        }

        Event::listen(RefundCreated::class, function ($event) {
            CreateReversedTransaction::dispatch($event->refundable, $event->refund, app(AccountPayout::class));
        });
    }

    public function register()
    {
        $this->app->register(CurrenciesServiceProvider::class);
        $this->app->register(QueryKitServiceProvider::class);
        $this->app->singleton(PaymentGatewayContract::class, function () {
        });
        $this->app->singleton(SalesAccountContract::class, SalesAccount::class);
    }
}
