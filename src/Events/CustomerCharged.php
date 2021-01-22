<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Transaction;

class CustomerCharged
{
    use Dispatchable, SerializesModels;

    /**
     * @var CustomerContract
     */
    public $customer;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param CustomerContract $customer
     * @param Transaction      $transaction
     */
    public function __construct($customer, $transaction)
    {
        $this->customer = $customer;
        $this->transaction = $transaction;
    }
}
