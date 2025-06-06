<?php

namespace App\Exceptions;

class WalletNotFoundException extends WalletException
{
    /**
     * Create a new wallet not found exception instance
     *
     * @param string $message
     * @return void
     */
    public function __construct(string $message = 'Wallet not found')
    {
        parent::__construct($message, 404); // 404 Not Found
    }
}
