<?php

namespace VSoft\LaravelEscrow\Adapters\PaymentGetway;

use VSoft\LaravelEscrow\Contracts\RefundContract;

class PGWTransferReversal implements RefundContract
{
    use HasAmount;
}
