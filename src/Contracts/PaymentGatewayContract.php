<?php

namespace VSoft\LaravelEscrow\Contracts;

use Illuminate\Database\Eloquent\Model;
use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Escrow;

interface PaymentGatewayContract
{
    /**
     * @param CustomerContract $customer
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     *
     * @return Model
     */
    public function charge($customer, $amount, $associatedEscrow = null);

    /**
     * @param ProviderContract $provider
     * @param Amount           $amount
     * @param Escrow | null    $associatedEscrow
     *
     * @return Model
     */
    public function pay($provider, $amount, $associatedEscrow = null);

    /**
     * @param RefundableContract $provider
     * @param Amount | null      $amount
     *
     * @return Model
     */
    public function refund($refundable, $amount = null);
}
