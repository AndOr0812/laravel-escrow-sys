<?php

namespace VSoft\LaravelEscrow\Interactions;

use VSoft\LaravelEscrow\Escrow;
use VSoft\LaravelEscrow\EscrowStatus;
use VSoft\LaravelEscrow\Events\EscrowCommitted;
use VSoft\LaravelEscrow\Exceptions\IllegalEscrowAction;

class CommitEscrow
{
    /**
     * @param Escrow $escrow
     */
    public function handle($escrow)
    {
        throw_unless($escrow->checkStatus(new EscrowStatus('open')), IllegalEscrowAction::class);

        Interact::call(DepositEscrow::class, $escrow, $escrow->escrowable->getDepositAmount()->subtract($escrow->getBalance()));

        $escrow->committed_at = now();
        $escrow->save();

        event(new EscrowCommitted($escrow));
    }
}
