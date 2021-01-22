<?php

namespace VSoft\LaravelEscrow\Interactions;

use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Contracts\SalesAccountContract;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Events\SalesAccountDeposited;
use VSoft\LaravelEscrow\TransactionTypes\PlatformFee;

class DepositSalesAccount
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     */
    public function handle($escrow, $amount)
    {
        if ($amount->toCents() !== 0 && app()->bound(SalesAccountContract::class)) {
            $transaction = $escrow->withdraw(
                $escrow->escrowable->getCustomerAmount()->subtract($escrow->escrowable->getProviderAmount()),
                $salesAccount = app(SalesAccountContract::class),
                function ($transaction) {
                    $transaction->setType(app(PlatformFee::class));
                }
            );
            event(new SalesAccountDeposited($salesAccount, $transaction));
        }
    }
}
