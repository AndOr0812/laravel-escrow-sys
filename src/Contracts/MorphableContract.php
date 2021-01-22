<?php

namespace VSoft\LaravelEscrow\Contracts;

interface MorphableContract
{
    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @return mixed
     */
    public function getMorphClass();
}
