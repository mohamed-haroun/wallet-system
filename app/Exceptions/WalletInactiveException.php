<?php

namespace App\Exceptions;

class WalletInactiveException extends WalletException
{
    /**
     * Create a new wallet inactive exception instance
     *
     * @param string $message
     * @return void
     */
    public function __construct(string $message = 'Wallet is inactive')
    {
        parent::__construct($message, 403); // 403 Forbidden
    }
}
