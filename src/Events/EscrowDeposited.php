<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Transaction;

class EscrowDeposited
{
    use SerializesModels;

    /**
     * @var Escrow
     */
    public $escrow;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @param Escrow      $escrow
     * @param Transaction $transaction
     */
    public function __construct($escrow, $transaction)
    {
        $this->escrow = $escrow;
        $this->transaction = $transaction;
    }
}
