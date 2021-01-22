<?php

namespace VSoft\LaravelEscrow\Contracts;

use VSoft\LaravelCurrencies\Amount;
use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\Transaction;

interface TransactableContract extends MorphableContract
{
    /**
     * @param Amount $amount
     * @param $source
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function deposit($amount, $source, $associatedEscrow = null);

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function deposits();

    /**
     * @return Amount
     */
    public function getBalance();

    /**
     * @param Amount $amount
     * @param $destination
     * @param Escrow | null $associatedEscrow
     *
     * @return Transaction
     */
    public function withdraw($amount, $destination, $associatedEscrow = null);

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function withdrawals();
}
