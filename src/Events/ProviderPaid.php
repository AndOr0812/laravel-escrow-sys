<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Transaction;

class ProviderPaid
{
    use Dispatchable, SerializesModels;

    /**
     * @var ProviderContract
     */
    public $provider;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param ProviderContract $provider
     * @param Transaction      $transaction
     */
    public function __construct($provider, $transaction)
    {
        $this->provider = $provider;
        $this->transaction = $transaction;
    }
}
