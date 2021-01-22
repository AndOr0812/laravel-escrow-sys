<?php

namespace VSoft\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Events\ProviderPaid;
use VSoft\LaravelEscrow\TransactionTypes\AccountPayout;
use VSoft\LaravelEscrow\TransactionTypes\TransactionType;

class CreateProviderPayout
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $provider;
    public $amount;
    public $associatedEscrow;
    public $transactionType;

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     * @param TransactionType | string | null $transactionType
     */
    public function __construct($provider, $amount, $associatedEscrow = null, $transactionType = null)
    {
        $this->provider = $provider;
        $this->amount = $amount ?: $provider->getBalance();
        $this->associatedEscrow = $associatedEscrow;
        $this->transactionType = $transactionType;
    }

    public function handle()
    {
        if ($this->amount->gt(Amount::zero())) {
            $payout = app(PaymentGateway::class)->pay($this->provider, $this->amount, $this->associatedEscrow);

            ProviderPaid::dispatch($this->provider, $this->provider->withdraw($this->amount, $payout, function ($transaction) {
                $transaction->setType(app(AccountPayout::class));
            }));
        }
    }
}
