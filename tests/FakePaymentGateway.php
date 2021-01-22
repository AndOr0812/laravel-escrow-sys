<?php

namespace VSoft\LaravelEscrow\Tests;

use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract;
use VSoft\LaravelEscrow\Tests\Fakes\PaymentGateway;

trait FakePaymentGateway
{
    public function setUp()
    {
        parent::setUp();

        // Bind fake payment gateway
        app()->singleton(PaymentGatewayContract::class, PaymentGateway::class);
    }
}
