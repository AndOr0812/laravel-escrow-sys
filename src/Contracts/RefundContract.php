<?php

namespace VSoft\LaravelEscrow\Contracts;

use VSoft\LaravelCurrencies\Amount;

interface RefundContract
{
    /**
     * @return Amount
     */
    public function getAmount();
}
