<?php

namespace VSoft\LaravelEscrow\Contracts;

use VSoft\LaravelStripeObjects\StripeAccount;

interface ProviderContract extends TransactableContract
{
    /**
     * @return StripeAccount
     */
    public function stripeAccount();
}
