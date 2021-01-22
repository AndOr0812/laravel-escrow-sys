<?php

namespace VSoft\LaravelEscrow\Tests\Fakes;

use App\User;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Transactable;
use VSoft\LaravelStripeObjects\HasStripeCustomer;

class Customer extends User implements CustomerContract
{
    use HasStripeCustomer, Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
