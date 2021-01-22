<?php

namespace VSoft\LaravelEscrow\Tests\Feature;

use Illuminate\Support\Facades\Event;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract;
use VSoft\LaravelEscrow\Contracts\SalesAccountContract;
use VSoft\LaravelEscrow\Events\EscrowDeposited;
use VSoft\LaravelEscrow\Events\EscrowFunded;
use VSoft\LaravelEscrow\Events\EscrowReleased;
use VSoft\LaravelEscrow\Exceptions\IllegalEscrowAction;
use VSoft\LaravelEscrow\TransactionTypes\FinalEscrowDeposit;
use VSoft\LaravelEscrow\TransactionTypes\PlatformFee;
use VSoft\LaravelEscrow\TransactionTypes\ProviderPayment;
use VSoft\LaravelEscrow\Tests\DatabaseTestCase;
use VSoft\LaravelEscrow\Tests\FakePaymentGateway;

class ReleaseEscrowTest extends DatabaseTestCase
{
    use FakePaymentGateway;

    /** @test **/
    public function it_cant_release_until_committed()
    {
        $this->expectException(IllegalEscrowAction::class);
        $this->escrow->release();

        $this->escrow->commit()->release();
        $this->assertEquals('released', $this->escrow->status->get());
    }

    /** @test **/
    public function it_fails_to_release_if_cant_charge_full_amount()
    {
        $this->escrow->commit();

        app(PaymentGatewayContract::class)->shouldFail();

        $this->expectException(\Exception::class);
        $this->escrow->release();
    }

    /** @test **/
    public function it_charges_the_remaining_amount()
    {
        $this->escrow->commit()->release();

        $this->assertTrue($this->escrow->deposits->get(0)->amount->equals($this->product->getDepositAmount()));
        $this->assertTrue($this->escrow->deposits->get(1)->amount->equals($this->product->getCustomerAmount()->subtract($this->product->getDepositAmount())));
    }

    /** @test **/
    public function it_transfers_funds_to_provider_and_sales_account()
    {
        $this->escrow->commit()->release();

        list($providerAmount, $feeAmount) = [
            $this->product->getProviderAmount(),
            $this->product->getCustomerAmount()->subtract($this->product->getProviderAmount()),
        ];

        $this->assertTrue($this->escrow->getBalance()->equals(Amount::zero()));
        $this->assertTrue($this->provider->getBalance()->equals($providerAmount));

        $this->assertTrue(app(SalesAccountContract::class)->getBalance()->equals($feeAmount));
    }

    /** @test **/
    public function it_labels_transactions_when_releasing_escrow()
    {
        $this->escrow->commit()->release();

        $this->assertInstanceOf(FinalEscrowDeposit::class,
            $this->escrow->deposits()->latest('id')->first()->type()
        );

        $this->assertInstanceOf(ProviderPayment::class,
            $this->provider->deposits()->first()->type()
        );

        $this->assertInstanceOf(PlatformFee::class,
            $this->escrow->withdrawals()->destinationIs(app(SalesAccountContract::class))->first()->type()
        );
    }

    /** @test **/
    public function it_dispatches_events_when_releasing()
    {
        $this->escrow->commit();

        Event::fake();

        $this->escrow->release();

        Event::assertDispatched(EscrowDeposited::class);
        Event::assertDispatched(EscrowFunded::class);
        Event::assertDispatched(EscrowReleased::class);
    }
}
