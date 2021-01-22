<?php

namespace VSoft\LaravelEscrow\Tests\Feature;

use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Tests\DatabaseTestCase;
use VSoft\LaravelEscrow\Tests\FakePaymentGateway;
use VSoft\LaravelEscrow\Tests\Fakes\Product;

class EscrowRepositoryTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function an_escrow_can_be_created_through_the_facade()
    {
        $this->assertInstanceOf(Escrow::class, $this->escrow);
    }

    /** @test **/
    public function it_can_find_an_existing_escrow()
    {
        $this->assertTrue(\Escrow::findOrFail($this->product, $this->customer, $this->provider)->is($this->escrow));
    }

    /** @test **/
    public function it_can_find_an_escrow_from_the_escrowable_alone()
    {
        $this->assertTrue(\Escrow::findOrFail($this->product)->is($this->escrow));
    }

    /** @test **/
    public function it_can_find_or_create_an_escrow()
    {
        // Find existing
        $this->assertTrue(\Escrow::findOrCreate($this->product, $this->customer, $this->provider)->is($this->escrow));

        // Create new
        $this->assertFalse(\Escrow::findOrCreate(Product::create([]), $this->customer, $this->provider)->is($this->escrow));
    }
}
