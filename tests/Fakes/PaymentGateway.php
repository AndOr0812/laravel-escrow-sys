<?php

namespace VSoft\LaravelEscrow\Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Adapters\Stripe\StripeCharge;
use VSoft\LaravelEscrow\Adapters\Stripe\StripeRefund;
use VSoft\LaravelEscrow\Adapters\Stripe\StripeTransfer;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Contracts\PaymentGatewayContract;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Contracts\RefundableContract;
use VSoft\LaravelEscrow\Events\RefundCreated;
use VSoft\LaravelStripeObjects\StripeObject;
use Stripe\Charge;
use Stripe\Refund;
use Stripe\Transfer;

class PaymentGateway implements PaymentGatewayContract
{
    protected $shouldFail = false;
    protected $refundAmount = null;

    public function __construct()
    {
        $this->refundAmount = (new Product())->getDepositAmount();
    }

    /**
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param $reference
     *
     * @return StripeObject
     */
    public function charge($customer, $amount, $reference = null)
    {
        $this->maybeFail();

        return StripeCharge::createFromObject(new Charge(uniqid()));
    }

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param $reference
     *
     * @return StripeObject
     */
    public function pay($provider, $amount, $reference = null)
    {
        $this->maybeFail();

        return StripeTransfer::createFromObject(new Transfer(uniqid()));
    }

    /**
     * @param RefundableContract $refundable
     * @param Amount | null      $amount
     *
     * @return Model
     */
    public function refund($refundable, $amount = null)
    {
        $this->maybeFail();

        return tap(StripeRefund::createFromObject(new Refund(uniqid())), function ($refund) use ($refundable) {
            $refund->data = [
                'amount' => $this->refundAmount->toCents(),
                'currency' => $this->refundAmount->currency()->getCode(),
            ];

            RefundCreated::dispatch($refund, $refundable);
        });
    }

    /**
     * @param Amount $amount
     */
    public function setRefundAmount(Amount $amount)
    {
        $this->refundAmount = $amount;
    }

    /**
     * @return PaymentGateway
     */
    public function shouldFail()
    {
        $this->shouldFail = true;

        return $this;
    }

    /**
     * @return PaymentGateway
     *
     * @throws \Exception
     */
    public function maybeFail()
    {
        if ($this->shouldFail) {
            throw new \Exception();
        }

        return $this;
    }
}
