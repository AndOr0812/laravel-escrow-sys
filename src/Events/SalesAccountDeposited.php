<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Transaction;

class SalesAccountDeposited
{
    use SerializesModels;

    /**
     * @var
     */
    public $salesAccount;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param $salesAccount
     * @param Transaction $transaction
     */
    public function __construct($salesAccount, $transaction)
    {
        $this->salesAccount = $salesAccount;
        $this->transaction = $transaction;
    }
}
