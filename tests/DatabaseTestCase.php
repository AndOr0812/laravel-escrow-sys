<?php

namespace VSoft\LaravelEscrow\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Tests\Fakes\Customer;
use VSoft\LaravelEscrow\Tests\Fakes\Product;
use VSoft\LaravelEscrow\Tests\Fakes\Provider;
use VSoft\LaravelStripeObjects\StripeCharge;
use Stripe\Charge;

class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Escrow
     */
    protected $escrow;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Provider
     */
    protected $provider;

    public function setUp()
    {
        parent::setUp();

        $this->escrow();
    }

    /**
     * @return Escrow
     */
    public function escrow()
    {
        return $this->escrow = \Escrow::create(
            $this->product = Product::create([]),
            $this->customer = factory(Customer::class)->create(),
            $this->provider = factory(Provider::class)->create()
        );
    }

    /**
     * @return \VSoft\LaravelStripeObjects\StripeObject
     */
    public function charge()
    {
        return StripeCharge::createFromObject(new Charge(1));
    }
}
