<?php

namespace VSoft\LaravelEscrow\Tests\Fakes;

use App\User;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Transactable;
use VSoft\LaravelStripeObjects\HasStripeAccount;

class Provider extends User implements ProviderContract
{
    use HasStripeAccount, Transactable;

    /**
     * @var string
     */
    protected $table = 'users';
}
