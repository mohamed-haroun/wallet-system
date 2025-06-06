<?php

namespace App\Exceptions;

class InsufficientFundsException extends WalletException
{
    /**
     * Create a new insufficient funds exception instance
     *
     * @param string $message
     * @return void
     */
    public function __construct(string $message = 'Insufficient funds in wallet')
    {
        parent::__construct($message, 402); // 402 Payment Required
    }
}
