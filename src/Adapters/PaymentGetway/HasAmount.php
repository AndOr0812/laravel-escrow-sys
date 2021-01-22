<?php

namespace VSoft\LaravelEscrow\Adapters\Stripe;

use VSoft\LaravelCurrencies\Amount;

trait HasAmount
{
    /**
     * @return Amount
     */
    public function getAmount()
    {
        return Amount::fromCents(
            data_get($this->data, 'amount'),
            data_get($this->data, 'currency')
        );
    }
}
