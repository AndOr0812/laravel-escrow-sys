<?php

namespace VSoft\LaravelEscrow;

use Illuminate\Database\Eloquent\Model as Eloquent;
use VSoft\EloquentStatus\HasStatus;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Contracts\EscrowableContract;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Contracts\TransactableContract;
use VSoft\LaravelEscrow\Exceptions\IllegalEscrowAction;
use VSoft\LaravelEscrow\Exceptions\InsufficientFunds;
use VSoft\LaravelEscrow\Interactions\CancelEscrow;
use VSoft\LaravelEscrow\Interactions\CommitEscrow;
use VSoft\LaravelEscrow\Interactions\Interact;
use VSoft\LaravelEscrow\Interactions\ReleaseEscrow;

class Escrow extends Eloquent implements TransactableContract
{
    use HasStatus,
        Transactable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function customer()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function escrowable()
    {
        return $this->morphTo('escrowable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function provider()
    {
        return $this->morphTo();
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param $query
     * @param CustomerContract $customer
     *
     * @return mixed
     */
    public function scopeCustomer($query, $customer)
    {
        return $query
            ->where('customer_type', $customer->getMorphClass())
            ->where('customer_id', $customer->getKey());
    }

    /**
     * @param $query
     * @param EscrowableContract $escrowable
     *
     * @return mixed
     */
    public function scopeEscrowable($query, $escrowable)
    {
        return $query
            ->where('escrowable_type', $escrowable->getMorphClass())
            ->where('escrowable_id', $escrowable->getKey());
    }

    /**
     * @param $query
     * @param ProviderContract $provider
     *
     * @return mixed
     */
    public function scopeProvider($query, $provider)
    {
        return $query
            ->where('provider_type', $provider->getMorphClass())
            ->where('provider_id', $provider->getKey());
    }

    // _________________________________________________________________________________________________________________

    /**
     * @param bool $payoutDeposits
     *
     * @return Escrow
     */
    public function cancel($payoutDeposits = true)
    {
        Interact::call(CancelEscrow::class, $this, $payoutDeposits);

        return $this;
    }

    /**
     * @return Escrow
     */
    public function commit()
    {
        Interact::call(CommitEscrow::class, $this);

        return $this;
    }

    /**
     * @return mixed
     */
    public function isFunded()
    {
        return $this->getBalance()->gte($this->escrowable->getDepositAmount());
    }

    /**
     * @return Escrow
     *
     * @throws InsufficientFunds
     * @throws IllegalEscrowAction
     */
    public function release()
    {
        Interact::call(ReleaseEscrow::class, $this);

        return $this;
    }

    // _________________________________________________________________________________________________________________

    /**
     * @return \VSoft\EloquentStatus\Status
     */
    public function getStatusAttribute()
    {
        return EscrowStatus::guess($this);
    }

    /**
     * @return string
     */
    public function getIdentifierAttribute()
    {
        return class_basename($this)."#{$this->id}";
    }
}
