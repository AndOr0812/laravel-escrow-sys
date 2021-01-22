<?php

namespace VSoft\LaravelEscrow\Events;

use Illuminate\Queue\SerializesModels;
use VSoft\LaravelEscrow\Escrow;

class EscrowFunded
{
    use SerializesModels;

    /**
     * @var Escrow
     */
    public $escrow;

    /**
     * @param Escrow $escrow
     */
    public function __construct($escrow)
    {
        $this->escrow = $escrow;
    }
}
