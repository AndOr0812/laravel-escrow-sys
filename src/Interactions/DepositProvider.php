<?php

namespace VSoft\LaravelEscrow\Interactions;

use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Events\ProviderDeposited;
use VSoft\LaravelEscrow\TransactionTypes\ProviderPayment;
use VSoft\LaravelEscrow\TransactionTypes\TransactionType;

class DepositProvider
{
    /**
     * @param Escrow $escrow
     * @param Amount $amount
     * @param TransactionType | string $transactionType
     */
    public function handle($escrow, $amount, $transactionType = null)
    {
        if ($amount->toCents() > 0) {
            ProviderDeposited::dispatch(
                $escrow->provider,
                $escrow->provider->deposit($amount, $escrow, function ($transaction) use ($transactionType) {
                    $transaction->setType($transactionType ?: app(ProviderPayment::class));
                })
            );
        }
    }
}
