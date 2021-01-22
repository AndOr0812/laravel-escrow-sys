<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Contracts\RefundableContract;
use VSoft\LaravelEscrow\Contracts\RefundContract;

class RefundCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var RefundContract
     */
    public $refund;

    /**
     * @var RefundableContract
     */
    public $refundable;

    /**
     * @param RefundableContract $refundable
     * @param RefundContract     $refund
     */
    public function __construct($refund, $refundable)
    {
        $this->refund = $refund;
        $this->refundable = $refundable;
    }
}
