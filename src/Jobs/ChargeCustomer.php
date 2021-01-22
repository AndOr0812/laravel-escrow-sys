<?php

namespace VSoft\LaravelEscrow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract as PaymentGateway;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Events\CustomerCharged;
use VSoft\LaravelEscrow\TransactionTypes\AccountDeposit;
use VSoft\LaravelEscrow\TransactionTypes\TransactionType;

class ChargeCustomer
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer;
    public $amount;
    public $associatedEscrow;
    public $transactionType;

    /**
     * @param CustomerContract $customer
     * @param Amount $amount
     * @param Escrow | null $associatedEscrow
     * @param TransactionType | string | null $transactionType
     */
    public function __construct($customer, $amount, $associatedEscrow = null, $transactionType = null)
    {
        $this->customer = $customer;
        $this->amount = $amount;
        $this->associatedEscrow = $associatedEscrow;
        $this->transactionType = $transactionType;
    }

    public function handle()
    {
        if ($this->amount->gt(Amount::zero())) {
            $charge = app(PaymentGateway::class)->charge($this->customer, $this->amount, $this->associatedEscrow);

            CustomerCharged::dispatch($this->customer, $this->customer->deposit($this->amount, $charge, function ($transaction) {
                $transaction->setAssociatedEscrow($this->associatedEscrow);
                $transaction->setType($this->transactionType ?: app(AccountDeposit::class));
            }));
        }
    }
}
