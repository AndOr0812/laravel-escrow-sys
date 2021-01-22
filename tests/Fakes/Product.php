<?php

namespace VSoft\LaravelEscrow\Tests\Fakes;

use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Contracts\EscrowableContract;
use VSoft\LaravelEscrow\Escrowable;

class Product extends \Illuminate\Database\Eloquent\Model implements EscrowableContract
{
    use Escrowable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return Amount
     */
    public function getDepositAmount()
    {
        return new Amount(250); // often the deposit will be the platforms' fee
    }

    /**
     * @return Amount
     */
    public function getCustomerAmount()
    {
        return new Amount(1000);
    }

    /**
     * @return Amount
     */
    public function getProviderAmount()
    {
        return new Amount(750);
    }
}
