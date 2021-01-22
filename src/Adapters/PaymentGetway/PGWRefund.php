<?php

namespace VSoft\LaravelEscrow\Adapters\PaymentGetway;

use VSoft\LaravelEscrow\Contracts\RefundContract;

class PGWRefund implements RefundContract
{
    use HasAmount;
}
