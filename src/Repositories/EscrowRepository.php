<?php

namespace VSoft\LaravelEscrow\Repositories;

use Exception;
use VSoft\LaravelEscrow\Contracts\CustomerContract;
use VSoft\LaravelEscrow\Contracts\EscrowableContract;
use VSoft\LaravelEscrow\Contracts\ProviderContract;
use VSoft\LaravelEscrow\Escrow;

class EscrowRepository
{
    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $customer
     * @param ProviderContract   $provider
     *
     * @return Escrow
     */
    public function findOrFail($escrowable, $customer = null, $provider = null)
    {
        return app(Escrow::class)->newQuery()
            ->escrowable($escrowable)
            ->when($customer, function ($query) use ($customer) {
                $query->customer($customer);
            })
            ->when($provider, function ($query) use ($provider) {
                $query->provider($provider);
            })
            ->firstOrFail();
    }

    /**
     * @param $escrowable
     * @param $customer
     * @param $provider
     * @return Escrow
     */
    public function findOrCreate($escrowable, $customer, $provider)
    {
        try {
            return $this->findOrFail($escrowable, $customer, $provider);
        } catch (Exception $e) {
            return $this->create($escrowable, $customer, $provider);
        }
    }

    /**
     * @param EscrowableContract $escrowable
     * @param CustomerContract   $customer
     * @param ProviderContract   $provider
     *
     * @return Escrow
     */
    public function create($escrowable, $customer, $provider)
    {
        return app(Escrow::class)->create([
            'escrowable_type' => $escrowable->getMorphClass(),
            'escrowable_id' => $escrowable->getKey(),
            'customer_type' => $customer->getMorphClass(),
            'customer_id' => $customer->getKey(),
            'provider_type' => $provider->getMorphClass(),
            'provider_id' => $provider->getKey(),
        ]);
    }
}
